// pages/assess/assess.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    stars: [
      "/images/s_star.png",
      "/images/star.png",
      "/images/star.png",
      "/images/star.png",
      "/images/star.png",
    ],
    score:1,
    options:{}
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var that = this;
    that.setData({
      options:options
    })
  },

  assess: function(e) {
    var that = this;
    var k = e.currentTarget.dataset.k;
    var arr = that.data.stars;
    console.log(k)
    for (var i = k; i <= 4; i++) {
      arr[i] = "/images/star.png";
    }
    for (var i = 0; i <= k; i++) {
      arr[i] = "/images/s_star.png";
    }
    that.setData({
      stars:arr,
      score:k+1
    })
  },
  formsubmit:function(e){
    var that = this;
    var content = e.detail.value.content;
    console.log(e)
    console.log(that.data.score)
    app.loginstatus(function(res){
      if(res){
        var data = {
          uid:res.uid,
          token:res.token,
          oid:that.data.options.oid,
          fid: that.data.options.fid,
          score: that.data.score,
          content: content,
        };
        httprequest.httpPost("fixedAssess",data,function(res){
          console.log(res)
          if(res.status == 1){
            wx.showToast({
              title: '评价成功',
            })
            setTimeout(()=>{
              wx.navigateBack({
                delta: 1,
              })
            },2000)
          }else{
            wx.showToast({
              icon:"none",
              title: res.msg,
            })
          }
        })
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