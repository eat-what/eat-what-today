import axios from 'axios';
import config from './configs';

const instance = axios.create({
  baseURL: config.baseURL,
});

console.log(config);

axios.interceptors.request.use((configs) => {
  return configs;
}, (error) => {
  return Promise.reject(error);
});

axios.interceptors.response.use((response) => {
  return response;
}, (error) => {
  return Promise.reject(error);
});

export function get({ url = '', params = {} }) {
  return instance.request({
    method: 'get',
    url,
    params,
  });
}

export function post({ url = '', data = {}, noEmpty = false }) {
  return instance.request({
    method: 'post',
    url,
    data,
  });
}
