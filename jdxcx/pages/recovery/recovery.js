// pages/recovery/recovery.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    mark: 1,
    height: 0,
    top: null,
    tophidden: true,
    hideload: true,
    hiddennotice: true,
    ishidden: true,
    nomore: false,
    page: 2,
    limit: 10,
    keyword: "",
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    app.getaddress(function(e) {
      if (e) {
        that.setData({
          curarea: e.regeocodeData.addressComponent.district
        })
        that.getlist();
      } else {
        that.onLoad();
      }
    })
  },
  /**
   * 下拉刷新
   */
  onPullDownRefresh: function() {
    var that = this;
    console.log("刷新")
    that.setData({
      hideload: false,
      page: 2,
      list: {},
      nomore: false,

    })
    that.getlist();
  },
  getlist: function() {
    var that = this;
    that.setData({
      list: {}
    })
    // 获取维修服务列表
    app.getaddress(function(e) {
      if (e) {
        var data = {
          latitude: e.latitude,
          longitude: e.longitude,
          mark: that.data.mark,
          keyword:that.data.keyword,
        };

        httprequest.httpPost('getRecoveryList', data, function(res) {
          console.log("返回的数据", res)
          setTimeout(() => {
            wx.hideLoading();
            wx.stopPullDownRefresh()
            if (res.status == 1) {
              that.setData({
                list: res.data,
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
        that.getlist();
      }
    })
  },

  actionsearch: function (e) {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    console.log(e)
    var keyword = e.detail.value.keyword;
    that.setData({
      keyword: keyword,
    })
    console.log(that.data.where)
    that.getlist()
  },

  onReachBottom: function() {
    var that = this;

    if (!that.data.nomore && that.data.ishidden) {
      that.setData({
        ishidden: false,
      })
      app.getaddress(function(e) {
        if (e) {

          var data = {
            page: that.data.page,
            limit: that.data.limit,
            latitude: e.latitude,
            longitude: e.longitude,
            keyword: that.data.keyword,
            mark: that.data.mark
          };

          httprequest.httpPost('getRecoveryList', data, function(res) {
            console.log("返回的数据", res)
            setTimeout(() => {
              if (res.status == 1) {
                var arr = that.data.list.concat(res.data);
                var page = ++that.data.page;
                that.setData({
                  list: arr,
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
        } else {
          that.onReachBottom();
        }
      })
    }
  },

  scroll: function(e) {
    console.log(e.detail.scrollTop)
    var scrollTop = e.detail.scrollTop
    if (scrollTop >= 500) {
      this.setData({
        tophidden: false
      })
    } else {
      this.setData({
        tophidden: true
      })
    }
  },
  change: function(e) {
    var mark = e.currentTarget.dataset.mark;
    this.setData({
      mark: mark,
      page: 2,
      nomore: false,
      hiddennotice: true,
    })
  },

  tohome: function() {
    app.tohome();
  },

  totop: function() {
    this.setData({
      top: 0
    })
  },

  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },
})