import Vue from 'vue';
import _ from 'lodash';
import $ from 'jquery';
import axios from 'axios'

declare global {
    var Vue = Vue;
    var _ = _;
    var $ = $;
    var jQuery = $;
    var axios = axios;
}
