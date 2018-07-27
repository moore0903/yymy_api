import CryptoJS from 'crypto-js'

export default {
  install(Vue, options) {

    Vue.prototype.Decrypt = function(data) {
      var key = CryptoJS.enc.Utf8.parse('e75e2228a8036523');
      var decrypted = CryptoJS.AES.decrypt(data.data, key, {
        mode: CryptoJS.mode.ECB,
        padding: CryptoJS.pad.Pkcs7
      });
      var result = JSON.parse(decrypted.toString(CryptoJS.enc.Utf8));
      return result;
    }

    Vue.prototype.imgHost = function(data) {
      if (data) {
        var result = data.replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi, function(match, capture) {
          var newStr = '<img src="https://api.whzhuoban.xyz' + capture + '" alt="" width="100%" height="" />';
          return newStr;
        });
      } else {
        var result = data;
      }

      return result;
    }

    Vue.prototype.brReplace = function(data) {
      var result = data;
      if (data) {
        result = data.replace('\r\n', '<br/>');
      }
      return result;
    }



    Vue.prototype.htmlChar = function(data) {
      var res = data
      var res1 = res.replace(/<\/?.+?>/g, "");
      var res2 = res1.replace(/ /g, "");
      return res2
    }



  }
}
