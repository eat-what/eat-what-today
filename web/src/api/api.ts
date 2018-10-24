import { get } from './request';

export function getVideo() {
  return get({
    url: '/video',
  });
}

export function getNews(params: any) {
  return get({
    url: '/news',
    params,
  });
}
