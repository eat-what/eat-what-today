import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import './registerServiceWorker';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

import { titleMixin } from './util/mixins';
import {
  timeAgo,
  host,
} from './util/filters';

Vue.config.productionTip = false;
Vue.use(ElementUI);

Vue.mixin(titleMixin);
Vue.filter('timeAgo', timeAgo);
Vue.filter('host', host);

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#app');
