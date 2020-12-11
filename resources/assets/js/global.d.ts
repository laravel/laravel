import Vue from 'vue';
import Component from 'vue-class-component';

import _ from 'lodash';
import $ from 'jquery';
import axios, {AxiosResponse as Res, AxiosError as Err} from 'axios'

declare global {
    var Vue = Vue;
    var Component = Component;

    var _ = _;
    var $ = $;
    var jQuery = $;
    var axios = axios;
    type AxiosResponse = Res;
    type AxiosError = Err;
}
