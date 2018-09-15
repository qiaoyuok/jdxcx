//app.js
var httprequest = require("utils/httpRequest.js");
var amapFile = require('./utils/amap-wx.js'); //如：..­/..­/libs/amap-wx.js

App({

  onLaunch: function() {
    const updateManager = wx.getUpdateManager()
    updateManager.onCheckForUpdate(function (res) {
      // 请求完新版本信息的回调
      //console.log(res.hasUpdate)
    })
    updateManager.onUpdateReady(function() {
      wx.showModal({
        title: '更新提示',
        content: '新版本已经准备好，是否重启应用？',
        success: function(res) {
          if (res.confirm) {
            // 新的版本已经下载好，调用 applyUpdate 应用新版本并重启
            updateManager.applyUpdate()
          }
        }
      })

    })

    updateManager.onUpdateFailed(function() {
      // 新的版本下载失败
      //console.log("新的版本下载失败")
    })
    var that = this;
    var systeminfo = wx.getSystemInfoSync();
    that.globalData.systeminfo = systeminfo;
  },

  // 获取经纬度信息
  getaddress: function(cb) {
    var myAmapFun = new amapFile.AMapWX({
      key: '87e743c13c928291752bd3beab564967'
    });
    myAmapFun.getRegeo({
      success: function(data) {
        //成功回调
        console.log("获取到的位置详细信息", data[0])
        typeof cb == "function" && cb(data[0])

      },
      fail: function(info) {
        //失败回调
        typeof cb == "function" && cb(false)
      }
    })
  },

  // 判断是否是登录态
  loginstatus: function(cb) {
    var that = this;
    wx.getStorage({
      key: 'logininfo',
      success: function(res) {
        if (Date.parse(new Date()) > res.data.outtime) {
          wx.removeStorage({
            key: "logininfo"
          })
          typeof cb == "function" && cb(false);
        } else {
          typeof cb == "function" && cb(res.data);
        }
      },
      fail: function() {
        typeof cb == "function" && cb(false);
      }
    })
  },

  // 用户登录操作
  login: function(e, cb) {
    var that = this;
    wx.showLoading({
      title: '登陆中...',
    })
    console.log("手机号加密信息", e)
    // 获取code
    wx.login({
      success: function(res) {
        console.log(res)
        var data = {
          code: res.code,
          iv: e.detail.iv,
          encryptedData: e.detail.encryptedData,
          flag:3
        }
        console.log(res)
        if (e.detail.errMsg == "getPhoneNumber:ok") {
          console.log("允许授权")
          // 信息提交到后台
          httprequest.httpPost("userlogin", data, function(res) {
            console.log(res)
            wx.hideLoading();
            if (res.status) {
              var data = {
                uid: res.uid,
                token: res.token,
              };
              wx.setStorage({
                key: "logininfo",
                data: data
              })
              typeof cb == "function" && cb(true);
            } else {
              wx.showToast({
                icon: "none",
                title: res.msg,
              })
              typeof cb == "function" && cb(false);
            }
          });
        } else {
          wx.hideLoading();
          wx.showToast({
            icon: "none",
            title: '登录失败,请重试',
          })
          typeof cb == "function" && cb(false);
        }
      }
    })
  },

  tohome: function() {
    console.log("返回首页")
    wx.switchTab({
      url: "/pages/index/index"
    })
  },

  globalData: {
    userInfo: null,
    systeminfo: null,
    addressinfo: null,
  },
})