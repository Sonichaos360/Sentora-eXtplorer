<?php
/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 *
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class module_controller extends ctrl_module
{
    //<@ UserPass @>
    static function getUserPass()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $random = self::getRandomString(20);
        $random_pass = md5($random);
        $username = $currentuser["username"];

        //.htusers path in filemanager
        $file = '/etc/sentora/panel/etc/apps/filemanager/config/.htusers.php';

        //If exits remove old extplorer user
        $lines = file($file);
        $lineSearch = -1;

        foreach ($lines as $lineNumber => $line) 
        {
            if (strpos($line, $username) !== false) 
            {
                $lineSearch = $lineNumber;
            }

            if($lineSearch != -1)
            {
                unset($lines[$lineNumber]);
                $arr = array_values($lines);
                //writing to file
                file_put_contents($file, implode($arr));
                break;
            }
        }

        //Renew extplorer user
        $content = stream_get_contents(fopen($file, "r"));

        //Add user
        $content = str_replace("//The array", "//The array \n         array('".$username."','".$random_pass."','/var/sentora/hostdata/".$username."/','','1','','1',1),", $content);

        fwrite(fopen($file, "w"),$content);

        return $random;
    }

   static function getUserName()
   {
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser["username"];
   }

   //Return random string for token
   static function getRandomString($numero)
   {
        $caracter= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        srand((double)microtime()*1000000);
        $rand = null;
        for($i=0; $i<$numero; $i++) {
            $rand .= $caracter[rand()%strlen($caracter)];
        }
        return $rand;
    }
}
