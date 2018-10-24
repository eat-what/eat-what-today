import { merge } from 'lodash';

const env = process.env.NODE_ENV || 'development';
const configs: any = {};

configs.default = {
  baseURL: 'http://127.0.0.1:7001',
};

configs.development = {
  baseURL: 'http://127.0.0.1:7001',
};

configs.production = {
  baseURL: 'http://127.0.0.1:7001',
};

const config: any = merge( configs.default, configs[env] );

(window as any).__config__ = config;

export default config;
