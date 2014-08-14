<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PanelController
 *
 * @author Web Developer
 */
class Backend_PanelController extends GuvenlikController {

    //put your code here

        public function getIndex(){
            return View::make('Backend.admin.index');
        }
        
        public function getAyarlar(){
            return View::make('Backend.admin.ayarlar');
        }

}
