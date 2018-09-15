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
    realname:"",
    tel:"",
    detail:"",
    id:"",
  },

  formSubmit: function (e) {
    var that = this;
    console.log(e)
    app.loginstatus(function (res) {
      if (res) {
        var data = {
          uid: res.uid,
          token: res.token,
          latitude: that.data.latitude,
          longitude: that.data.longitude,
          address: that.data.address,
          name: that.data.name,
          detail: e.detail.value.detail,
          tel: e.detail.value.tel,
          id: that.data.id,
          realname: e.detail.value.realname
        }
        console.log(data)
        httprequest.httpPost("editaddress", data, function (res) {
          if (res.status == 1) {
            wx.showToast({
              title: res.msg,
            })
            wx.navigateBack({
              delta: 1
            })
          } else if (res.status == -1) {
            wx.navigateTo({
              url: '/pages/login/login',
            })
          } else {
            wx.showToast({
              icon: "none",
              title: res.msg,
            })
          }
        })
      } else {
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
        // 获取该地址详情
        var data = {
          uid :e.uid,
          token:e.token,
          id: options.id
        }
        httprequest.httpPost('getaddressdetail',data,function(res){
          if(res.status == 1){
            that.setData({
              address: res.data.address,
              name: res.data.name,
              longitude: res.data.longitude,
              latitude: res.data.latitude,
              realname: res.data.realname,
              tel: res.data.tel,
              detail: res.data.detail,
              id: options.id,
            })
          }else{
            wx.navigateBack({
              delta:1
            })
          }
        })
      } else {
        wx.navigateTo({
          url: '/pages/login/login',
        })
      }
    })
  },
})