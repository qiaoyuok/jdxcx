// pages/my_publish/my_publish.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    mark: 0,
    height: 0,
    option: "fixed",
    list: [],
    recovery_data: [],
    options: null,
    hiddennotice: true,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    wx.showLoading({
      title: '加载中...',
    })
    var that = this;
    that.setData({
      height: 750 / app.globalData.systeminfo.windowWidth * app.globalData.systeminfo.windowHeight - 120,
      options: options,
      list:{}
    })

    that.getinfo();
  },

  getinfo: function() {
    var that = this;
    app.getaddress(function(e) {
      console.log("获取到的位置信息", e)
      if (e) {
        // 获取我的维修服务列表
        app.loginstatus(function(res) {
          if (res) {
            var data = {
              uid: res.uid,
              token: res.token,
              latitude: e.latitude,
              longitude: e.longitude,
              mark:that.data.mark
            }
            httprequest.httpPost("getMyPublishList", data, function(res) {
              console.log(res)
              setTimeout(() => {
                if (res.status == 1) {
                  that.setData({
                    list: res.data,
                  })
                } else {
                  that.setData({
                    hiddennotice: false,
                  })
                }
                wx.hideLoading();
              }, 1000)
            })
          } else {
            wx.navigateTo({
              url: '/pages/login/login',
            })
          }
        })
      } else {
        that.onLoad();
      }
    })
  },

  down:function(e){
    var that = this;
    var id = e.currentTarget.dataset.id;

    wx.showModal({
      title: '下架服务提醒',
      content: '下架后该服务将不会被附近的人看到！',
      success: function (res) {
        console.log(res)
        if (res.confirm) {
          app.loginstatus(function (e) {
            var data = {
              uid: e.uid,
              token: e.token,
              id: id,
              mark: that.data.mark
            };

            httprequest.httpPost("downPublish", data, function (res) {
              console.log(res)
              if (res.status == 1) {
                wx.showToast({
                  title: '下架成功',
                })
                setTimeout(() => {
                  that.onLoad(that.data.options);
                }, 300)
              } else {
                wx.showToast({
                  icon: "none",
                  title: '下架失败',
                })
              }
            })
          })
        }
      }
    })
  },

  up: function (e) {
    var that = this;
    var id = e.currentTarget.dataset.id;
    app.loginstatus(function (e) {
      var data = {
        uid: e.uid,
        token: e.token,
        id: id,
        mark: that.data.mark
      };

      httprequest.httpPost("upPublish", data, function (res) {
        console.log(res)
        if (res.status == 1) {
          wx.showToast({
            title: '上架成功',
          })
          setTimeout(() => {
            that.onLoad(that.data.options);
          }, 300)
        } else {
          wx.showToast({
            icon: "none",
            title: '上架失败',
          })
        }
      })
    })
  },

  del: function(e) {
    var that = this;
    var id = e.currentTarget.dataset.id;

    wx.showModal({
      title: '删除提醒',
      content: '确定要删除该条服务吗？',
      success: function(res) {
        console.log(res)
        if (res.confirm) {
          app.loginstatus(function(e) {
            var data = {
              uid: e.uid,
              token: e.token,
              id: id,
              mark: that.data.mark
            };

            httprequest.httpPost("delPublish", data, function(res) {
              console.log(res)
              if (res.status == 1) {
                wx.showToast({
                  title: '删除成功',
                })
                setTimeout(()=>{
                  that.onLoad(that.data.options);
                },300)
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

  change: function(e) {
    var that= this;
    var mark = e.currentTarget.dataset.mark;
    var option = e.currentTarget.dataset.option;
    wx.showLoading({
      title: '加载中...',
    })
    that.setData({
      mark: mark,
      option: option,
      hiddennotice: true,
      list:{}
    })
    that.getinfo();
  },

  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },

  add: function() {
    var that = this;
    var url = "./add_" + that.data.option + "/add_" + that.data.option;
    console.log(url)
    console.log(that.data)
    if (that.data.options.status != 1 && that.data.mark == 0) {
      wx.showToast({
        icon: "none",
        title: '发布维修服务需先完成门店认证',
      })
    } else {
      wx.navigateTo({
        url: url
      })
    }
  },
})