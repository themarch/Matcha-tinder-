const utils = {}

utils.formatCity = function(str) {
  return str.toLowerCase().replace(/[-']/g, ' ')
    .replace(/[éèêëėęē]/g, 'e')
    .replace(/[àáâäãæ]/g, 'a')
    .replace(/[çćč]/g, 'c');
}

export {
  utils
}