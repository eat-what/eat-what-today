export function host(url: any): any {
  if (!url) {
    return '';
  }
  const h = url.replace(/^https?:\/\//, '').replace(/\/.*$/, '');
  const parts = h.split('.').slice(-3);
  if (parts[0] === 'www') {
    parts.shift();
  }
  return parts.join('.');
}

export function timeAgo(time: any): any {
  const between = Date.now() / 1000 - Number(time);
  if (between < 3600) {
    return pluralize((between / 60), ' minute');
  } else if (between < 86400) {
    return pluralize((between / 3600), ' hour');
  } else {
    return pluralize((between / 86400), ' day');
  }
}

function pluralize(time: any, label: any): any {
  const roundedTime = Math.round(time);
  if (roundedTime === 1) {
    return roundedTime + label;
  }
  return roundedTime + label + 's';
}
