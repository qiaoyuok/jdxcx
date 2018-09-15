// pages/my_publish/add_fixed/add_fixed.js
const app = getApp();
var httprequest = require("../../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    height: 0,
    imglist: [],
    servimglist: [],
    ishidden: true,
    userinfo: {}
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    wx.showLoading({
      title: '加载中...',
    })
    that.setData({
      options:options
    })
    app.loginstatus(function (e) {
      if (e) {
        var data = {
          uid:e.uid,
          token:e.token,
          id:options.id
        };
        
        httprequest.httpGet("editRecovery",data,function(res){
          console.log(res)
          setTimeout(()=>{
            wx.hideLoading();
            if (res.status == 1) {
              that.setData({
                recoverydetail: res.data,
                latitude: res.data.latitude,
                longitude: res.data.longitude,
                address:res.data.address,
                name:res.data.name
              })
              if (res.data.pics.length > 0) {
                that.setData({
                  ishidden: false,
                  imglist: res.data.pics,
                  servimglist: res.data.serv,
                })
              }
            }
          },500)
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

  chooseimg: function (e) {
    var that = this;
    var length = that.data.servimglist.length;
    if (length <= 2) {
      wx.chooseImage({
        count: 1, // 默认9
        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
        success: function (res) {
          wx.showLoading({
            title: '上传中...',
          })
          // 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
          var tempFilePaths = res.tempFilePaths[0]

          httprequest.httpUpload("upload", tempFilePaths, {}, function (res) {

            if (res.status == 1) {
              var arr = that.data.imglist;
              arr.push(tempFilePaths)
              that.setData({
                ishidden: false,
                imglist: arr
              })
              that.data.servimglist.push({
                url: res.filename
              });
              console.log(that.data.servimglist)

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
    } else {
      wx.showToast({
        icon: "none",
        title: '最多上传3张图片哦！',
      })
    }
  },

  del: function (e) {
    var n = 0;
    var that = this;
    var id = e.currentTarget.dataset.id;
    var arr = that.data.imglist;
    var servarr = that.data.servimglist;
    arr.splice(id, 1)
    servarr.splice(id, 1)
    var length = arr.length;
    console.log(length)
    if (length == 0) {
      that.setData({
        ishidden: true
      })
    }
    that.setData({
      imglist: arr,
      servimglist: servarr
    })

    console.log("本地", that.data.imglist)
    console.log("服务端", that.data.servimglist)

  },

  formSubmit: function (e) {
    console.log(e)
    var that = this;
    console.log(that.data.servimglist)
    var that = this;
    var pics = JSON.stringify(that.data.servimglist);
    console.log(pics)
    app.loginstatus(function (res) {
      if (res) {
        var data = {
          id:that.data.options.id,
          uid: res.uid,
          token: res.token,
          pics: pics,
          realname: e.detail.value.realname,
          tel: e.detail.value.tel,
          wx: e.detail.value.wx,
          address: e.detail.value.address,
          name: e.detail.value.name,
          detail: e.detail.value.detail,
          des: e.detail.value.des,
          latitude: that.data.latitude,
          longitude: that.data.longitude
        };
        wx.showLoading({
          title: '提交中...',
        })

        httprequest.httpPost("editRecovery", data, function (res) {
          setTimeout(() => {
            if (res.status == -1) {
              wx.navigateTo({
                url: '/pages/login/login',
              })
            } else if (res.status == 1) {
              wx.showToast({
                title: res.msg,
              })
            } else {
              wx.showToast({
                icon: "none",
                title: res.msg,
              })
            }
            wx.hideLoading();
          }, 500)
        })
      } else {
        wx.navigateTo({
          url: '/pages/login/login',
        })
      }
    })

  },

})