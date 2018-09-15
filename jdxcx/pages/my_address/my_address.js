// pages/my_address/my_address.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");

Page({

  /**
   * 页面的初始数据
   */
  data: {
    height: null,
    addresslist: null,
    hiddennotice: true,
    hideload: true,
    isback: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var that = this;
    if (options.isback) {
      that.setData({
        isback: true
      })
    }
    that.setData({
      height: 750 / app.globalData.systeminfo.windowWidth * app.globalData.systeminfo.windowHeight - 120
    })
  },
  /**
   * 下拉刷新
   */
  onPullDownRefresh: function() {
    var that = this;
    that.setData({
      hideload: false
    })
    that.onShow();
    setTimeout(() => {
      wx.stopPullDownRefresh()
      that.setData({
        hideload: true
      })
    }, 1000)
  },
  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },

  choose: function(e) {
    var that = this;
    var id = e.currentTarget.dataset.id;
    app.loginstatus(function(res) {
      if (res) {
        var data = {
          uid: res.uid,
          token: res.token,
          id: id,
        };

        httprequest.httpPost("chooseaddress", data, function(res) {
          if (res.status == 1) {
            if (that.data.isback) {
              wx.navigateBack({
                delta: 1
              })
            } else {
              // that.onShow();
            }
          }
        })
      } else {

      }
    })
  },

  del: function(res) {
    var id = res.currentTarget.dataset.id;
    var that = this;
    wx.showModal({
      title: '删除提示',
      content: '确定要删除吗？',
      success: function(e) {
        console.log(e)
        if (e.confirm) {
          app.loginstatus(function(res) {
            var data = {
              uid: res.uid,
              token: res.token,
              id: id,
            }
            httprequest.httpPost('deladdress', data, function(res) {
              if (res.status == 1) {
                wx.showToast({
                  title: '删除成功',
                })
                that.onShow();
              } else {
                wx.showToast({
                  icon: "none",
                  title: '删除失败',
                })
              }
            })
          })
        }
      }
    })
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    app.loginstatus(function(e) {
      if (e) {
        var data = {
          uid: e.uid,
          token: e.token,
        };

        httprequest.httpPost("getaddresslist", data, function(res) {
          console.log(res)
          setTimeout(() => {
            wx.hideLoading();
            if (res.status == 1) {
              that.setData({
                addresslist: res.data,
                hiddennotice: true
              })
            } else if (res.status == -1) {
              wx.navigateTo({
                url: '/pages/login/login',
              })
            } else {
              that.setData({
                addresslist: {},
                hiddennotice: false
              })
            }
          }, 300)
        })
      }
    })
  },
})