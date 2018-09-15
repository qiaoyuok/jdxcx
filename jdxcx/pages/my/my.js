// pages/my/my.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");

Page({

  /**
   * 页面的初始数据
   */
  data: {
    defaultimg: "/images/avatar.jpg",
    nickname: "我是小能手",
    sex:0,
    status: 0,
  },

  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    if (url == 'pages/about/about') {
      wx.navigateTo({
        url: url,
      })
    } else {
      app.loginstatus(function(res) {
        if (res) {
          wx.navigateTo({
            url: url,
          })
        } else {
          wx.navigateTo({
            url: '/pages/login/login',
          })
        }
      })
    }
  },

  login: function(e) {
    var that = this;
    app.login(e, function(res) {
      if (res) {
        that.onShow();
      }
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },
  clear:function(){
    console.log("清除")
    wx.clearStorage()
    app.globalData.loginstatus = null;
    wx.showToast({
      title: '清除成功',
    })
    this.onShow();
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    var that = this;
    app.loginstatus(function(res) {
      if (!res) {
        that.setData({
          islogin: false
        })
      } else {
        that.setData({
          islogin: true
        })

        // 获取用户信息
        var data = {
          uid: res.uid,
          token: res.token,
        }

        httprequest.httpPost("getuserinfo", data, function(res) {
          console.log("返回的数据", res)
          if (res.data.avatarurl) {
            that.setData({
              avatarurl: res.data.avatarurl,
            })
          }
          that.setData({
            nickname: res.data.nickname,
            status: res.data.status,
            sex:res.data.sex
          })
        })

        console.log(that.data)
      }
    });
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

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },


})