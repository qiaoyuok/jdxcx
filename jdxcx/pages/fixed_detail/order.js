// pages/fixed_detail/order.js
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");

Page({

  /**
   * 页面的初始数据
   */
  data: {
    tmpaddress:"选择地址",
    realname: "",
    tel:"",
    addressdetail:{},
    fid:null,
  },
  formSubmit: function(e) {
    var that = this;
    console.log(e)
    app.loginstatus(function(res){
      if(res){
        var data = {
          uid:res.uid,
          token:res.token,
          addressdetail: JSON.stringify(that.data.addressdetail),
          remark:e.detail.value.remark,
          fid: that.data.fid,
        };
        httprequest.httpPost("addFixedOrder",data,function(res){
          console.log(res)
          if(res.status == 1){
            wx.showToast({
              title: res.msg,
            })
            setTimeout(()=>{
              wx.navigateBack({
                delta:1
              })
            },2000)
          }else{
            wx.showToast({
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
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    console.log(options)
    var that = this;
    that.setData({
      fid:options.fid
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
    var that =this;
    app.loginstatus(function(e){
      if(e){
        var data = {
          uid:e.uid,
          token:e.token,
        }
        httprequest.httpPost("getDefaultAddressDetail",data,function(res){
          if(res.status == -1){
            wx.navigateTo({
              url: '/pages/login/login',
            })
          }else if(res.status == 1){
            console.log(res)
            var tmpaddress = res.data.address + res.data.name + res.data.detail
            that.setData({
              addressdetail:res.data,
              tmpaddress: tmpaddress,
              realname: res.data.realname,
              tel: res.data.tel,
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

  redirect: function (e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
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