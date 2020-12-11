import Vue from 'vue';
import Component from 'vue-class-component';
import _ from 'lodash';
import $ from 'jquery';
import axios from 'axios'

declare global {
    var Vue = Vue;
    var Component = Component;
    var _ = _;
    var $ = $;
    var jQuery = $;
    var axios = axios;
}
