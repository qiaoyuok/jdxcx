// pages/my_order/order_detail.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");

Page({

  /**
   * 页面的初始数据
   */
  data: {
    height: 0,
    fixedlist: {},
    orderinfo: {},
    options: null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var that = this;
    that.setData({
      height: 750 / app.globalData.systeminfo.windowWidth * app.globalData.systeminfo.windowHeight - 110,
      options: options
    })
    that.getinfo();
  },

  getinfo: function() {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    app.getaddress(function(address) {
      // 获取当前预约详情
      app.loginstatus(function(e) {
        if (e) {
          var data = {
            uid: e.uid,
            token: e.token,
            id: that.data.options.id,
            latitude: address.latitude,
            longitude: address.longitude
          };

          httprequest.httpPost("getorderdetail", data, function(res) {
            console.log(res)
            setTimeout(()=>{
              wx.hideLoading();
              if (res.status == 1) {
                that.setData({
                  orderinfo: res.data,
                  fixedlist: res.data.fixedinfo
                })
              }
            },200)
          })
        } else {
          wx.navigateTo({
            url: '/pages/login/login',
          })
        }
      })
    })
  },

  option: function(e) {
    var status = e.currentTarget.dataset.status;
    var that = this;
    if (status == 5) {
      wx.showModal({
        title: '取消预约',
        content: '确定要取消本次预约吗？',
        success: function(res) {
          console.log(res)
          if (res.confirm) {
            that.changestatus(status)
          }
        }
      })
    } else if (status == 3) {
      wx.showModal({
        title: '服务提醒',
        content: '确定服务已经完成？',
        success: function(res) {
          console.log(res)
          if (res.confirm) {
            that.changestatus(status)
          }
        }
      })
    }
  },

  changestatus: function(status) {
    var that = this;
    app.loginstatus(function(e) {
      var data = {
        uid: e.uid,
        token: e.token,
        id: that.data.options.id,
        status: status,
      };

      httprequest.httpPost('changeOrderStatus', data, function(res) {
        console.log(res)
        that.getinfo();
      })
    })
  },

  tocall: function(e) {
    var tel = e.currentTarget.dataset.tel;
    wx.makePhoneCall({
      phoneNumber: tel //仅为示例，并非真实的电话号码
    })
  },
  copy: function(e) {
    var wxid = e.currentTarget.dataset.wx;
    wx.setClipboardData({
      data: wxid,
      success: function(res) {
        console.log(res)
        if (res.errMsg == "setClipboardData:ok") {
          wx.showToast({
            title: '微信号复制成功',
          })
        }
      }
    })

  },
  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },
  getlocation: function() {

    var that = this;
    wx.openLocation({
      latitude: that.data.orderinfo.addressdetail.latitude,
      longitude: that.data.orderinfo.addressdetail.longitude,
      scale: 14
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