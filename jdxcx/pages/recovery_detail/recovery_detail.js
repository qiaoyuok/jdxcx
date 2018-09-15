// pages/recovery_detail/recovery_detail.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    pics: [
      'http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg'
    ],
    isshare:false,
    collectstatus:false,
  },
  tocall: function (e) {
    var that = this;
    if (that.data.isshare) {
      var tel = e.currentTarget.dataset.tel;
      wx.makePhoneCall({
        phoneNumber: tel //仅为示例，并非真实的电话号码
      })
    } else {
      wx.showModal({
        title: '温馨提示',
        content: '通过左侧分享按钮转发后才可以拨打哦！',
      })
    }
  },
  getlocation: function () {

    var that = this;
    wx.openLocation({
      latitude: that.data.recoverdetail.latitude,
      longitude: that.data.recoverdetail.longitude,
      scale: 14
    })

  },
  getcollectstatus: function () {
    var that = this;
    app.loginstatus(function (res) {
      if (res) {
        var data = {
          uid: res.uid,
          token: res.token,
          flag: 2,
          s_id: that.data.recoverdetail.id
        }
        httprequest.httpPost("getcollectstatus", data, function (res) {
          console.log("收藏状态", res)
          if (res.status == 1) {
            that.setData({
              collectstatus: true
            })
          } else {
            that.setData({
              collectstatus: false
            })
          }
        })
      }
    })
  },
  collectoption: function () {
    var that = this;
    app.loginstatus(function (res) {
      if (res) {
        var data = {
          uid: res.uid,
          token: res.token,
          flag: 2,
          s_id: that.data.recoverdetail.id
        }
        httprequest.httpPost("collectoption", data, function (res) {
          console.log("操作返回结果", res)
          if (res.status == 1) {
            that.setData({
              collectstatus: !that.data.collectstatus
            })
          } else if (res.status == 0) {
            wx.showToast({
              icon: "none",
              title: res.msg,
            })
          } else {
            wx.navigateTo({
              url: '/pages/login/login',
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
  copy: function (e) {
    console.log(e)
    var that = this;
    if (that.data.isshare) {
      var wxid = e.currentTarget.dataset.wx;
      wx.setClipboardData({
        data: wxid,
        success: function (res) {
          if (res.errMsg == "setClipboardData:ok") {
            wx.showToast({
              title: '微信号复制成功',
            })
          }
        }
      })
    } else {
      wx.showModal({
        title: '温馨提示',
        content: '通过左侧分享按钮转发后即可直接复制！',
      })
    }
  },

  onShareAppMessage: function (res) {
    console.log(res)
    var that = this;
    return {
      imageUrl: that.data.recoverdetail['pics'][0],
      path: '/pages/recovery_detail/recovery_detail?id=' + that.data.options.id,
      complete: function (res) {
        console.log(res)
        if (res.errMsg == "shareAppMessage:ok") {
          var telkey = "recoverdetail.texttel";
          var wxkey = "recoverdetail.textwx";
          that.setData({
            isshare: true,
            [telkey]: that.data.recoverdetail.tel,
            [wxkey]:that.data.recoverdetail.wx,
          })
        }
      }
    }
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    console.log(options)
    var that = this;
    that.setData({
      options: options
    })
    wx.showLoading({
      title: '加载中...',
    })
    app.getaddress(function(e) {
      if (e) {
        var data = {
          id: options.id,
          longitude: e.longitude,
          latitude: e.latitude,
        };

        httprequest.httpPost("getRecoveryDetail", data, function(res) {
          console.log(res)
          console.log(res)
          setTimeout(() => {
            wx.hideLoading();
            that.setData({
              recoverdetail: res.data,
            })
            if (res.data.pics.length>0){
              that.setData({
                pics: res.data.pics
              })
            }
            that.getcollectstatus()
          },500)
          
        })
      } else {
        that.onLoad();
      }
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