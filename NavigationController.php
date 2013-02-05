<?php
/**
* @version 1.0.0 (05/02/2013)
* Please refer to the readme for help with using this class.
* @author Antony D'Andrea contactme@antonydandrea.com
* @copyright (c) 2012 Antony D'Andrea
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy of 
* this software and associated documentation files (the "Software"), to deal in the 
* Software without restriction, including without limitation the rights to use, copy, 
* modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, 
* and to permit persons to whom the Software is furnished to do so, subject to the 
* following conditions:
* 
* The above copyright notice and this permission notice shall be included in all 
* copies or substantial portions of the Software and the README.txt file.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
* INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
* PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION 
* OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
* SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
class NavigationController{
    
    /** 
     *
     * @var string 
     */
    private $dbName;
    
    /** 
     *
     * @var string 
     */
    private $tableName;
    
    /** 
     *
     * @var mysqliConnection 
     */
    private $connection;
    
    /** 
     * Holds all of the field names from the config file.
     * @var array 
     */
    private $fields = array();
    
    /** 
     * Config path required including the name of the file.
     * @param string $configPath
     * @param mysqliConnection $connection
     */
    public function __construct($configPath, $connection = null) 
    {
        if ($configPath == null) {
            die("Config file path required in constructor.");
        } 
        $config_array = $this->readJsonConfigFile($configPath);
        if (!empty($config_array)) {
            if (isset($config_array['databaseSettings'])) {
                $this->dbName = $config_array['databaseSettings']['dbName'];
                $this->tableName = $config_array['databaseSettings']['tableName'];
            }
            if (isset($config_array['databaseSettings']['fields'])) {
                $this->fields = $config_array['databaseSettings']['fields'];
            }
            
        }
        
        if ($connection !== null) {
            $this->connection = $connection;
        } else {
            if (isset ($config_array['databaseSettings']['host'])) {
                $host = $config_array['databaseSettings']['host'];
            } else {
                die("Missing the host from the config file.");
            }
            
            if (isset ($config_array['databaseSettings']['username'])) {
                 $username = $config_array['databaseSettings']['username'];;
            } else {
                die("Missing the username from the config file.");
            }
            
            if (isset ($config_array['databaseSettings']['password'])) {
                $password = $config_array['databaseSettings']['password'];
            } else {
                $password = "";
            }
            $this->connection = $this->connectToServer($host, $username, $password);
            mysqli_select_db($this->connection, $this->dbName) or die("Unable to connect to database");
        }
    }
    
    /** 
     * Returns a string of HTML code of an unordered list, which is actually
     * ordered based on the data.
     * @return string
     */
    public function renderMenu()
    {
        $menu = $this->loadMenuItems();
        if (empty($menu)) {
            echo "There are no items in the menu table ".$this->tableName;
            die();
        }
        $printableMenu = "";
        foreach ($menu as $key=>$menuItem) {
            $printableMenu .= $this->makePrintable($menuItem);
        }
        return $printableMenu;
    }
    
    /** 
     * 
     * @param string $host
     * @param string $username
     * @param string $password
     * @return mysqliConnection
     */
    private function connectToServer($host, $username, $password=""){
        $con = mysqli_connect($host, $username, $password);
        if ($con === false) {
             die("Connection to database failed. Err no: ".mysqli_connect_errno());
        }
        return $con;
    }
    
    /** 
     * Returns the settings from the config file in an array.
     * @param string $path
     * @return array
     */
    private function readJsonConfigFile($path)
    {
        $settingsArray = array();
        $jsonContent = file_get_contents($path);
        if ($jsonContent === false) {
            die("There was an error reading the config file.");
        }
        if ($jsonContent !== false) {
            $settingsArray = json_decode($jsonContent, true);
        }
        if (json_last_error()!== JSON_ERROR_NONE) {
            echo "An error occured with decoding the config: ".json_last_error();
            die();
        }
        return $settingsArray;
    }
    
    /** 
     * Turns a menu item into HTML code. Calls itself if the menu item has
     * subcategories of menu items.
     * @param array $menuItem
     * @return string
     */
    private function makePrintable($menuItem)
    {
        $printable = "<ul>";
            $printable .= "<li>";
                if ($menuItem[$this->fields['menu_href']] !== null) {
                    $printable .= "<a href='".$menuItem[$this->fields['menu_href']]."'>";
                }
                    $printable .= $menuItem[$this->fields['menu_label']];
                if (isset($menuItem[$this->fields['menu_href']]) && $menuItem[$this->fields['menu_href']] !== null) {
                    $printable .= "</a>";
                }

            if (isset($menuItem['submenus'])) {
                foreach ($menuItem['submenus'] as $key=>$category) {
                    $printable .= $this->makePrintable($category);
                }
            }
            $printable .= "</li>";
        $printable .= "</ul>";
        return $printable;
    }
    
    /** 
     * Calls functions that load the menu items and combines the subcategories
     * to parent menus.
     * @return array
     */
    private function loadMenuItems()
    {
        $complete_menu = array();
        $raw_menu_data = $this->loadAllMenus($this->tableName,$this->connection);       
        if (empty($raw_menu_data)) {
            echo "There are no items in the menu table ".$this->tableName;
            die();
        }
        $complete_menu = $this->combineArrays($raw_menu_data);
      
        return $complete_menu; 
    }
    
    /** 
     * Gets all of the data from the menu table.
     * @param string $tableName
     * @param mysqliConnection $connection
     * @return array
     */
    private function loadAllMenus ($tableName, $connection)
    {
        $menus = array();
        $query = "SELECT * FROM {$tableName} ORDER BY {$this->fields['menu_sort_order']} ASC;";
        
        if ($query === false) {
             die("Menu select failed. Err no: ".mysqli_connect_errno());
        }
        
        $rows = mysqli_query($connection, $query);
        if ($rows !== false && mysqli_num_rows($rows) > 0) {
            while($row = mysqli_fetch_array($rows)){
                if ($row[$this->fields['menu_category_menu_id']] == null) {
                    $menu_fk = 0;
                } else {
                    $menu_fk = $row[$this->fields['menu_category_menu_id']];
                }
                $pk = $row[$this->fields['menu_id']];
                $menus[$menu_fk][$pk] = $row;
            }
        }
        mysqli_close($this->connection); //closes the connection 
        return $menus;
    }
    
    /** 
     * Takes the menus and works out if certain menus are part of a subcategory
     * of a parent menu and combines them. Keeps calling itself until all
     * of the menus are sorted.
     * @param array $arrays
     * @param int $menuID
     * @return array
     */
    private function combineArrays($arrays, $menuID = 0)
    {
        $complete_menu = array();
        foreach ($arrays[$menuID] as $key=>$array) {
            if (isset($arrays[$key]) && $key != $menuID) {
               $array['submenus'] = $this->combineArrays($arrays, $key);
               $complete_menu[] = $array;
            } else {
                $complete_menu[] = $array;
            }
        }
        return $complete_menu;
    }
}