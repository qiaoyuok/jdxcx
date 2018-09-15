// pages/fixed_detail/fixed_detail.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    ishidden: true,
    defaultimg: "/images/avatar.jpg",
    map_option_text: "展开地图",
    height: 0,
    mark: 0,
    current: false,
    fixeddetail: {},
    markers: [],
    collectstatus: false,
    isshare: false,
    fid: null,
    startarr: [1, 0, 0, 0, 0],
    assesslist: [],
    page: 1,
    hiddennotice: true,
    nomore: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.showLoading({
      title: '加载中...',
    })
    var that = this;
    console.log(options)
    that.setData({
      height: 750 / app.globalData.systeminfo.windowWidth * app.globalData.systeminfo.windowHeight - 330,
      fid: options.id
    })
    app.getaddress(function (e) {
      console.log("获取的当前位置信息", e)
      if (e) {
        that.getassesslist();
        var data = {
          latitude: e.latitude,
          longitude: e.longitude,
          id: options.id
        };
        httprequest.httpPost("getfixeddetail", data, function (res) {
          console.log(res)
          var marks = {
            iconPath: "/images/wz.png",
            id: 0,
            latitude: res.data.latitude,
            longitude: res.data.longitude,
            width: 30,
            height: 30,
            callout: {
              content: res.data.address + "\n" + res.data.name + res.data.detail + "　　" + res.data.distance + "▶",
              padding: 20,
              bgColor: "#fff",
              color: "#000",
              display: "ALWAYS",
              fontSize: 15
            }
          }
          that.setData({
            fixeddetail: res.data,
            markers: [marks]
          })
          console.log(that.data.markers)
          wx.hideLoading();
          that.getcollectstatus();
        })
      } else {
        that.onLoad();
      }
    })
  },

  getassesslist: function (page = 1, limit = 10) {
    var that = this;
    var data = {
      fid: that.data.fid,
      page: page,
      limit: limit,
    };

    httprequest.httpPost('getAssessList', data, function (res) {
      console.log("获取的评论列表", res)
      setTimeout(() => {
        if (res.status == 1) {
          var arrlist = that.data.assesslist.concat(res.data.assesslist);
          var page = ++that.data.page;
          that.setData({
            assesslist: arrlist,
            page: page,
            hiddennotice: true,
            ishidden: true,
            assessinfo: res.data.assessinfo
          })
        } else {
          if (that.data.page == 1) {
            that.setData({
              hiddennotice: false,
            })
          } else {
            that.setData({
              hiddennotice: true,
              nomore: true,
              ishidden: true
            })
          }
        }
      }, 1000)
    })
  },

  collectoption: function () {
    var that = this;
    app.loginstatus(function (res) {
      if (res) {
        var data = {
          uid: res.uid,
          token: res.token,
          flag: 1,
          s_id: that.data.fixeddetail.fid
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

  getcollectstatus: function () {
    var that = this;
    app.loginstatus(function (res) {
      if (res) {
        var data = {
          uid: res.uid,
          token: res.token,
          flag: 1,
          s_id: that.data.fixeddetail.fid
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

  previewimg: function (e) {
    var that = this;
    wx.previewImage({
      current: e.currentTarget.dataset.url, // 当前显示图片的http链接
      urls: that.data.fixeddetail['pics'] // 需要预览的图片http链接列表
    })
  },

  previewimgidf: function (e) {
    var that = this;
    wx.previewImage({
      current: e.currentTarget.dataset.url, // 当前显示图片的http链接
      urls: [e.currentTarget.dataset.url] // 需要预览的图片http链接列表
    })
  },

  getlocation: function () {

    var that = this;
    wx.openLocation({
      latitude: that.data.fixeddetail.latitude,
      longitude: that.data.fixeddetail.longitude,
      scale: 14
    })

  },

  moveToLocation: function () {
    var that = this;
    if (that.data.current) {
      that.mapCtx.includePoints({
        padding: [10],
        points: [{
          latitude: that.data.fixeddetail.latitude,
          longitude: that.data.fixeddetail.longitude,
        }]
      })
      this.setData({
        current: false
      })
    } else {
      this.mapCtx.moveToLocation()
      this.setData({
        current: true
      })
    }
  },

  fixedtab: function (e) {
    var mark = e.currentTarget.dataset.mark;
    this.setData({
      mark: mark
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    this.mapCtx = wx.createMapContext('myMap')
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
    var that = this;
    if (that.data.mark == 2 && that.data.page >= 2 && !that.data.nomore) {
      console.log("加载更多")
      var that = this;
      that.setData({
        ishidden: false
      })
      that.getassesslist(that.data.page)
    }
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

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
      title: that.data.fixeddetail['shopname'],
      imageUrl: that.data.fixeddetail['pics'][0],
      path: '/pages/fixed_detail/fixed_detail?id=' + that.data.fid,
      complete: function (res) {
        console.log(res)
        if (res.errMsg == "shareAppMessage:ok") {
          that.setData({
            isshare: true,
          })
        }
      }
    }
  },

  order: function (e) {
    var that = this;
    app.loginstatus(function (res) {
      wx.navigateTo({
        url: '/pages/fixed_detail/order?fid=' + that.data.fixeddetail.fid,
      })
      // if (res && res.uid == that.data.fixeddetail.uid) {
      //   wx.showToast({
      //     icon: "none",
      //     title: '不能预约自己的服务哦!',
      //   })
      // } else {
      //   wx.navigateTo({
      //     url: '/pages/fixed_detail/order?fid=' + that.data.fixeddetail.fid,
      //   })
      // }
    })
  }

})