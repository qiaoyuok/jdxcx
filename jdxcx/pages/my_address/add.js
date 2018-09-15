// pages/my_address/add.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    address: "",
    name: "选择地址",
    longitude: 0,
    latitude: 0,
  },

  formSubmit: function (e) {
    var that = this;
    console.log(e)
    app.loginstatus(function (res) {
      if(res){
        var data = {
          uid:res.uid,
          token: res.token,
          latitude:that.data.latitude,
          longitude:that.data.longitude,
          address:that.data.address,
          name:that.data.name,
          detail: e.detail.value.detail,
          tel:e.detail.value.tel,
          realname:e.detail.value.realname
        }
        console.log(data)
        httprequest.httpPost("addaddress",data,function(res){
          if(res.status == 1){
            wx.showToast({
              title: res.msg,
            })
            wx.navigateBack({
              delta:1
            })
          }else if(res.status == -1){
            wx.navigateTo({
              url: '/pages/login/login',
            })
          }else{
            wx.showToast({
              icon: "none",
              title: res.msg,
            })
          }
        })
      }else{
        wx.navigateTo({
          url: '/pages/login/login',
        })
      }
    })
  },

  chooseadd: function () {
    var that = this;
    wx.chooseLocation({
      success: function (res) {
        console.log(res)
        that.setData({
          address: res.address,
          name: res.name,
          longitude: res.longitude,
          latitude: res.latitude
        })
      }
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    app.loginstatus(function (e) {
      if (e) {
        app.getaddress(function (res) {
          console.log(res)
          that.setData({
            address: res.regeocodeData.formatted_address,
            name: res.name,
            longitude:res.longitude,
            latitude:res.latitude
          })
        })
      } else {
        wx.navigateTo({
          url: '/pages/login/login',
        })
      }
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

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})