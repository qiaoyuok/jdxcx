// pages/apply/apply.js
const app = getApp();
var httprequest = require("../../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    yyzz: "/images/add_pic.png",
    idz: "/images/add_pic.png",
    idf: "/images/add_pic.png",
    yyzzr: "",
    idzr: "",
    idfr: "",
    address: null,
    latitude: null,
    longitude: null,
    name: null,
    canIUse: wx.canIUse('button.open-type.getUserInfo'),
    islogin: false,
    statushidden: true,
    statustext: null,
    color: "",
    detail:""
  },

  login: function (e) {
    var that = this;
    app.login(e, function (res) {
      if (res) {
        that.onShow();
      }
    })
  },
 
  formSubmit: function (e) {
    console.log(e)
    var that = this;
    app.loginstatus(function (res) {
      if (res) {
        var data = {
          uid: res.uid,
          token: res.token,
          name: e.detail.value.name,
          detail: e.detail.value.detail,
          address: e.detail.value.address,
          idf: e.detail.value.idf,
          idz: e.detail.value.idz,
          latitude: e.detail.value.latitude,
          longitude: e.detail.value.longitude,
          realname: e.detail.value.realname,
          shopname: e.detail.value.shopname,
          tel: e.detail.value.tel,
          wx: e.detail.value.wx,
          yyzz: e.detail.value.yyzz,
        }
        httprequest.httpPost("editshop", data, function (res) {
          if (res.status == 1) {
            wx.showToast({
              title: res.msg,
            })
          } else if (res.status == 0) {
            wx.showToast({
              icon: "none",
              title: res.msg,
            })
          }
        })
      }
    })

  },

  chooseimg: function (e) {
    var that = this;
    var img = e.currentTarget.dataset.img;
    var imgr = img + "r";
    wx.chooseImage({
      count: 1, // 默认9
      sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
      success: function (res) {
        wx.showLoading({
          title: '上传中...',
        })
        // 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
        var tempFilePaths = res.tempFilePaths
        console.log(tempFilePaths[0])
        var data = {
          type: 1
        };
        httprequest.httpUpload("upload", tempFilePaths[0], data, function (res) {
          console.log(res)
          if (res.status == 1) {
            that.setData({
              [img]: tempFilePaths[0],
              [imgr]: res.filename
            })
          } else {
            wx.showToast({
              icon: "none",
              title: '上传失败',
            })
          }
          wx.hideLoading();
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
          name: res.name,
          address: res.address,
          latitude: res.latitude,
          longitude: res.longitude
        })
      }
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    app.loginstatus(function(res){
      if(res){
        var data = {
          uid:res.uid,
          token:res.token,
        };

        httprequest.httpGet("editShop",data,function(res){
          console.log(res)
          that.setData({
            yyzz: res.data.yyzzr,
            idz: res.data.idzr,
            idf: res.data.idfr,
            yyzzr: res.data.yyzz,
            idzr: res.data.idz,
            idfr: res.data.idf,
            latitude: res.data.latitude,
            longitude: res.data.longitude,
            realname: res.data.realname,
            tel: res.data.tel,
            wx: res.data.wx,
            shopname: res.data.shopname,
            address: res.data.address,
            name: res.data.name,
            detail: res.data.detail,
          })
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