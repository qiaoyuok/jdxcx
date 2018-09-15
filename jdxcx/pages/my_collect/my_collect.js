// pages/my_publish/my_publish.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    mark: 1,
    fixedlist: {},
    hiddennotice: true,
    hideload: true,
    page: 2,
    limit: 10,
    ishidden: true,
    nomore: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    that.getinfo();
  },

  getinfo: function() {
    var that = this;
    app.loginstatus(function(e) {
      app.getaddress(function(res){
        if (e) {
          var data = {
            uid: e.uid,
            token: e.token,
            flag: that.data.mark,
            latitude: res.latitude,
            longitude: res.longitude,
          };
          httprequest.httpPost('getCollectList', data, function (res) {
            console.log(res)
            setTimeout(() => {
              wx.stopPullDownRefresh()
              wx.hideLoading();
              if (res.status == 1) {
                that.setData({
                  list: res.data,
                  hiddennotice: true,
                  hideload: true
                })
              } else if (res.status == 0) {
                that.setData({
                  list: {},
                  hiddennotice: false,
                  hideload: true
                })
                console.log(res.msg)
              } else {
                wx.navigateTo({
                  url: '/pages/login/login',
                })
              }
            }, 500)
          })
        } else {
          wx.navigateTo({
            url: '/pages/login/login',
          })
        }
      })
    })
  },

  /**
   * 下拉刷新
   */
  onPullDownRefresh: function() {
    var that = this;
    that.setData({
      hideload: false,
      nomore: false,
      page: 2,
      list: {},
    })
    that.getinfo();
  },

  onReachBottom: function () {
    console.log("加载了")
    var that = this;
    app.loginstatus(function (e) {
      app.getaddress(function(res){
        if (!that.data.nomore && that.data.ishidden) {
          var where = that.data.where;
          var data = {
            page: that.data.page,
            limit: that.data.limit,
            uid: e.uid,
            token: e.token,
            flag: that.data.mark,
            latitude: res.latitude,
            longitude: res.longitude,
          };
          that.setData({
            ishidden: false,
          })

          httprequest.httpPost('getCollectList', data, function (res) {
            console.log("返回的数据", res)
            setTimeout(() => {
              if (res.status == 1) {
                var arr = that.data.fixedlist.concat(res.data);
                var page = ++that.data.page;
                that.setData({
                  fixedlist: arr,
                  page: page,
                  ishidden: true,
                })
              } else {
                that.setData({
                  nomore: true,
                  ishidden: true
                })
              }
            }, 500)
          })
        }
      })
    })
  },
  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },
  change: function(e) {
    var that = this;
    var mark = e.currentTarget.dataset.mark;
    that.setData({
      mark: mark,
      list:{}
    })
    that.onLoad()
  },
})