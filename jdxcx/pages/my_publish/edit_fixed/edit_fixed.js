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
    ability:null,
    des:null,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.showLoading({
      title: '加载中...',
    })
    var that = this;
    that.setData({
      height: 750 / app.globalData.systeminfo.windowWidth * app.globalData.systeminfo.windowHeight - 90,
      options: options
    });

    app.loginstatus(function(res){
      var data = {
        uid:res.uid,
        token:res.token,
        id: options.id
      };

      httprequest.httpPost("getEditFixedDetail",data,function(res){
        console.log(res)
        setTimeout(()=>{
          wx.hideLoading();
          that.setData({
            imglist: res.data.pics,
            servimglist: res.data.serv,
            ability: res.data.ability,
            des: res.data.des
          })
          if (res.data.pics.length > 0) {
            that.setData({
              ishidden: false,
            })
          }
        },1000)

      })
    })
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
    if (length == 1) {
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
          uid: res.uid,
          token: res.token,
          id:that.data.options.id,
          pics: pics,
          ability: e.detail.value.ability,
          des: e.detail.value.des
        };

        httprequest.httpPost("editFixed", data, function (res) {
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
        })
      } else {
        wx.navigateTo({
          url: '/pages/login/login',
        })
      }
    })

  },
})