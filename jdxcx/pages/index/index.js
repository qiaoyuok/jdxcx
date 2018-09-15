//index.js
//获取应用实例
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");

Page({
  data: {
    imgUrls: [
      '/images/banner1.png',
      '/images/banner2.png',
    ],
    fixedlist: [],
    page: 2,
    limit: 10,
    addressinfo: null,
    ishidden: true,
    hideload: true,
    nomore: false,
    hiddennotice: true
  },
  onLoad: function() {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    that.getfixedlist();
  },

  getfixedlist: function() {
    var that = this;
    that.setData({
      fixedlist:{},
      hiddennotice: true
    })
    // 获取维修服务列表
    app.getaddress(function(e) {
      if (e) {
        var data = {
          latitude: e.latitude,
          longitude: e.longitude
        };

        httprequest.httpPost('getfixedlist', data, function(res) {
          console.log("返回的数据", res)
          setTimeout(() => {
            wx.hideLoading();
            wx.stopPullDownRefresh()
            if (res.status == 1) {
              that.setData({
                fixedlist: res.data,
                hideload: true,
                hiddennotice: true
              })
            } else {
              that.setData({
                hiddennotice: false,
                hideload: true
              })
            }

          }, 500)
        })
      } else {
        that.getfixedlist();
      }
    })
  },

  onReachBottom: function() {
    var that = this;
    if (!that.data.nomore && that.data.ishidden) {
      that.setData({
        ishidden: false
      })
      app.getaddress(function(e) {
        if (e) {

          var data = {
            page: that.data.page,
            limit: that.data.limit,
            latitude: e.latitude,
            longitude: e.longitude
          };


          httprequest.httpPost('getfixedlist', data, function(res) {
            console.log("返回的数据", res)
            setTimeout(() => {
              if (res.status == 1) {
                var arr = that.data.fixedlist.concat(res.data);
                var page = ++that.data.page;

                that.setData({
                  fixedlist: arr,
                  page: page,
                  ishidden: true
                })

              } else {
                that.setData({
                  nomore: true,
                  ishidden: true
                })
              }
            }, 500)
          })
        } else {
          that.onReachBottom();
        }
      })
    }
  },

  /**
   * 下拉刷新
   */
  onPullDownRefresh: function() {
    var that = this;
    that.setData({
      hideload: false,
      nomore: false,
      page: 2
    })
    that.getfixedlist();
  },

  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },
  fixeddetail: function(e) {
    var id = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: '/pages/fixed_detail/fixed_detail?id=' + id,
    })
  }
})