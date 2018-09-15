// pages/my/edit/edit.js
const app = getApp();
var httprequest = require("../../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    array: ['保密','男', '女'],
    sex:0,
    avatarurl:"http://img3.imgtn.bdimg.com/it/u=2392782296,2906787335&fm=27&gp=0.jpg",
    servimg:'',
  },

  chooseimg: function (e) {
    var that = this;
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
              avatarurl: tempFilePaths[0],
              servimg: res.filename
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

  save:function(e){
    console.log(e)
    app.loginstatus(function(res){
      if(res){
        var data = {
          uid: res.uid,
          token: res.token,
          sex: e.detail.value.sex,
          nickname:e.detail.value.nickname,
          avatarurl: e.detail.value.avatarurl,
          tel: e.detail.value.tel,
          wx: e.detail.value.wx,
          realname: e.detail.value.realname,
        };

        httprequest.httpPost("editUserinfo",data,function(res){
          console.log(res)
          wx.showToast({
            icon:"none",
            title: res.msg,
          })
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
      var data = {
        uid:res.uid,
        token:res.token
      };

      httprequest.httpGet("editUserinfo",data,function(res){
        console.log(res)
        if(res.status == 1){
          that.setData({
            nickname:res.data.nickname,
            sex:res.data.sex,
            realname:res.data.realname,
            tel:res.data.tel,
            wx:res.data.wx
          })

          if(res.data.avatarurl!=''){
            that.setData({
              avatarurl: res.data.avatarurl,
            })
          }
        }
      })
    })
  },
  bindPickerChange: function (e) {
    console.log('picker发送选择改变，携带值为', e.detail.value)
    this.setData({
      sex: e.detail.value
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
})