// pages/my_order/my_order.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");

Page({

  /**
   * 页面的初始数据
   */
  data: {
    statuscolor: "green",
    mark: 0,
    height: 0,
    fixedorderlist: null,
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
  onLoad: function (options) {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    that.getinfo();
    
  },

  getinfo: function () {
    var that = this;
    app.loginstatus(function (e) {
      if (e) {
        var data = {
          uid: e.uid,
          token: e.token,
          mark: that.data.mark,
        };
        httprequest.httpPost('getShopOrderList', data, function (res) {
          console.log(res)
          setTimeout(() => {
            wx.stopPullDownRefresh()
            wx.hideLoading();
            if (res.status == 1) {
              that.setData({
                fixedorderlist: res.data,
                hiddennotice: true,
                hideload: true
              })
            } else if (res.status == 0) {
              that.setData({
                fixedorderlist: {},
                hiddennotice: false,
                hideload: true
              })
              console.log(res.msg)
            } else {
              wx.navigateTo({
                url: '/pages/login/login',
              })
            }
          }, 1000)
        })
      } else {
        wx.navigateTo({
          url: '/pages/login/login',
        })
      }
    })
  },

  onReachBottom: function () {
    console.log("加载了")
    var that = this;
    app.loginstatus(function (e) {
      if (!that.data.nomore && that.data.ishidden) {
        var where = that.data.where;
        var data = {
          page: that.data.page,
          limit: that.data.limit,
          uid: e.uid,
          token: e.token,
          mark: that.data.mark,
        };
        that.setData({
          ishidden: false,
        })

        httprequest.httpPost('getShopOrderList', data, function (res) {
          console.log("返回的数据", res)
          setTimeout(() => {
            if (res.status == 1) {
              var arr = that.data.fixedorderlist.concat(res.data);
              var page = ++that.data.page;
              that.setData({
                fixedorderlist: arr,
                page: page,
                ishidden: true,
              })
            } else {
              that.setData({
                nomore: true,
                ishidden: true
              })
            }
          }, 1000)
        })
      }
    })
  },
  /**
   * 下拉刷新
   */
  onPullDownRefresh: function () {
    var that = this;
    that.setData({
      hideload: false,
      nomore: false,
      page: 2,
      fixedorderlist: {},
    })
    that.getinfo();
  },
  change: function (e) {
    var mark = e.currentTarget.dataset.mark;
    this.setData({
      mark: mark,
      fixedorderlist: {},
      hiddennotice:true
    })
    wx.showLoading({
      title: '加载中...',
    })
    this.getinfo();
  },

  redirect: function (e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },
})