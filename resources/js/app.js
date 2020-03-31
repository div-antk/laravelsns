// Laravelの全画面で共通的に使用することを想定したJS

import './bootstrap'
import Vue from 'vue'
import ArticleLike from './components/ArticleLike'

const app = new Vue({
  el: '#app',
  components: {
    ArticleLike,
  }
})