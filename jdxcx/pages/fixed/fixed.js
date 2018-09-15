// pages/fixed/fixed.js
let City = require('../../utils/allcity.js');
const app = getApp();
var httprequest = require("../../utils/httpRequest.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    list: ["box", "sort", "area", "serv"],
    boxData: {},
    sortData: {},
    areaData: {},
    serevData: {},
    sortStatus: false,
    areaStatus: false,
    servStatus: false,
    height: 0,
    hideload: true,
    top: null,
    tophidden: true,
    sortlist: [{
      value: "distance",
      name: "距离最近",
      check: 1
    }, {
      value: "assess",
      name: "评分最高",
      check: 0
    }, {
      value: "all",
      name: "综合排序",
      check: 0
    }],
    servlist: null,
    hiddennotice: true,
    sortHidden: true,
    areaHidden: true,
    servHidden: true,
    city: City,
    where: {
      sort: '',
      area: '',
      serv: '',
      keyword: ''
    },
    city_name: "区域",
    serv_name: "全部服务",
    sort_name: "距离最近",
    curarea: "",
    addressinfo: null,
    page: 2,
    limit: 10,
    ishidden: true,
    nomore: false,
  },

  donghua: function(e) {
    var oldheight = "90rpx";
    var name = e.currentTarget.dataset.name;
    var newheight = "100vh";
    console.log(newheight);
    var deg = 180;
    var duration = 100;
    // console.log(e.currentTarget.dataset.name)
    var list = this.data.list;
    var length = list.length;
    var objarr = [];
    for (var i = 0; i < length; i++) {
      objarr[list[i]] = wx.createAnimation({
        transformOrigin: "50% 50%",
        duration: duration,
        timingFunction: "linear",
        delay: 0
      })
    }
    for (var i = 0; i < length; i++) {
      if (list[i] == name) {
        var status = name + "Status";
        var data = name + "Data";
        var hidden = name + "Hidden"
        var that = this;
        // 该项已打开，直接复原状态，收缩box盒子
        if (this.data[status]) {
          objarr[name].rotate(0).step();
          objarr['box'].height(oldheight).step();
          this.setData({
            [data]: objarr[name].export(),
            [status]: false,
            boxData: objarr['box'].export(),
          })
          setTimeout(() => {
            that.setData({
              [hidden]: true
            })
          }, 200)
          break;
        } else {
          // 该项目前关闭状态；该项要转为打开态
          objarr[name].rotate(deg).step();
          objarr['box'].height(newheight).step();
          // 首先判断有没有其他的项已打开，有的话，需转为关闭状态
          for (var j = 0; j < length; j++) {
            // 找到已打开的其他项
            if (this.data[list[j] + "Status"]) {
              var data1 = list[j] + "Data";
              var status1 = list[j] + "Status";
              var hidden1 = list[j] + "Hidden";
              // 图标复原
              objarr[list[j]].rotate(0).step();
              this.setData({
                [data]: objarr[name].export(),
                [data1]: objarr[list[j]].export(),
                [status]: true,
                [status1]: false,
                [hidden]: false,
                [hidden1]: true,
              })
              break;
            }
          }
          // 遍历所有都没有打开的项，则直接打开该项
          this.setData({
            [data]: objarr[name].export(),
            [status]: true,
            boxData: objarr['box'].export(),
            [hidden]: false,
          })
          break;
        }
      }
    }
  },

  tohome: function() {
    app.tohome();
  },

  onReachBottom: function() {
    var that = this;
    if (!that.data.nomore && that.data.ishidden) {
      that.setData({
        ishidden: false,
      })
      app.getaddress(function(e) {
        if (e) {

          var where = that.data.where;
          var data = {
            page: that.data.page,
            limit: that.data.limit,
            latitude: e.latitude,
            longitude: e.longitude,
            sort: where.sort,
            area: where.area,
            serv: where.serv,
            keyword: where.keyword,
          };


          httprequest.httpPost('getfixedlist', data, function(res) {
            console.log("返回的数据", res)
            setTimeout(() => {
              if (res.status == 1) {
                var arr = that.data.fixedlist.concat(res.data);
                var page = ++that.data.page;
                that.setData({
                  fixedlist: arr,
                  page: page,
                  ishidden: true,
                })
              } else {
                that.setData({
                  nomore: true,
                  ishidden: true
                })
              }
            }, 500)
          })
        } else {
          that.onReachBottom();
        }
      })
    }

  },

  totop: function() {
    this.setData({
      top: 0
    })
  },
  /**
   * 下拉刷新
   */
  onPullDownRefresh: function() {
    var that = this;
    console.log("刷新")
    that.setData({
      hideload: false,
      page: 2
    })
    that.getfixedlist();
  },
  scroll: function(e) {
    console.log(e.detail.scrollTop)
    var scrollTop = e.detail.scrollTop
    if (scrollTop >= 500) {
      this.setData({
        tophidden: false
      })
    } else {
      this.setData({
        tophidden: true
      })
    }
  },

  choenv: function(e) {
    var that = this;
    var index = e.currentTarget.dataset.index;
    var flag = e.currentTarget.dataset.flag;
    var name = flag + "_name";
    var list = flag + "list";
    var key = list + "[" + index + "].check";

    var length = that.data[list].length;
    for (var i = 0; i < length; i++) {
      if (index != i) {
        if (that.data[list][i].check) {
          var tem_key = list + "[" + i + "].check";
          that.setData({
            [tem_key]: 0
          })
        }
      } else {
        that.setData({
          [key]: 1
        })
      }
    }
    var box = wx.createAnimation({
      transformOrigin: "50% 50%",
      duration: 100,
      timingFunction: "linear",
      delay: 0
    })

    var animation = wx.createAnimation({
      transformOrigin: "50% 50%",
      duration: 100,
      timingFunction: "linear",
      delay: 0
    })

    animation.rotate(0).step();
    box.height("90rpx").step();
    var key = flag + "Data";
    var status = flag + "Status";
    var hidden = flag + "Hidden";
    var where = "where." + flag;
    console.log(where)
    var value = that.data[list][index].value;
    var tmp_name = that.data[list][index].name;
    console.log("操作", name);
    console.log(value);
    that.setData({
      [key]: animation.export(),
      [status]: false,
      [hidden]: true,
      boxData: box.export(),
      [where]: value,
      [name]: tmp_name,
    })

    // console.log("此时的条件：", flag, that.data[list][index].value)
    // console.log("全局变量系统信息：", app.globalData.systeminfo)
    console.log(that.data.name)
    wx.showLoading({
      title: '加载中...',
    })
    that.getfixedlist()
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function() {
    var that = this;
    that.getserv();
    wx.showLoading({
      title: '加载中...',
    })
    app.getaddress(function(e) {
      if (e) {
        that.setData({
          curarea: e.regeocodeData.addressComponent.district
        })
        that.getfixedlist();
      } else {
        that.onLoad();
      }
    })
  },

  // 获取热门服务列表
  getserv: function() {
    var that = this;
    httprequest.httpPost('getserv', '', function(res) {
      that.setData({
        servlist: res.data
      })
    })
  },

  actionsearch: function(e) {
    wx.showLoading({
      title: '加载中...',
    })
    console.log(e)
    var keyword = e.detail.value.keyword;
    var that = this;
    var key = "where.keyword";

    that.setData({
      [key]: keyword
    })
    console.log(that.data.where)

    that.getfixedlist()

  },

  getfixedlist: function() {
    var that = this;
    that.setData({
      fixedlist: {}
    })
    app.getaddress(function(e) {
      // 获取维修服务列表
      if (e) {
        var data = {
          latitude: e.latitude,
          longitude: e.longitude,
          sort: that.data.where.sort,
          area: that.data.where.area,
          serv: that.data.where.serv,
          keyword: that.data.where.keyword,
        }

        httprequest.httpPost('getfixedlist', data, function(res) {
          console.log("返回的数据", res)
          setTimeout(() => {
            wx.stopPullDownRefresh();
            wx.hideLoading();
            if (res.status == 1) {
              that.setData({
                hiddennotice: true,
                fixedlist: res.data,
                hideload: true,
                nomore: false,
                page: 2
              })
            } else {
              that.setData({
                fixedlist: {},
                hiddennotice: false,
                hideload: true,
                nomore: false
              })
            }
          }, 500)
        })
      } else {
        that.getfixedlist();
      }
    })
  },

  cancel: function() {
    var that = this;
    var list = that.data.list;
    var deg = 180;
    var duration = 100;
    var length = list.length;
    var objarr = [];
    for (var i = 0; i < length; i++) {
      objarr[list[i]] = wx.createAnimation({
        transformOrigin: "50% 50%",
        duration: duration,
        timingFunction: "linear",
        delay: 0
      })
    }
    for (var i = 0; i < length; i++) {
      if (list[i] != "box") {
        if (that.data[list[i] + "Status"]) {
          var status = list[i] + "Status";
          var data = list[i] + "Data";
          var hidden = list[i] + "Hidden"
          // 首先判断有没有其他的项已打开，有的话，需转为关闭状态
          objarr[list[i]].rotate(0).step();
          objarr['box'].height("90rpx").step();
          this.setData({
            [data]: objarr[list[i]].export(),
            boxData: objarr['box'].export(),
            [status]: false,
          })
          setTimeout(() => {
            that.setData({
              [hidden]: true
            })
          }, 200)
        }
      }
    }
  },

  /**选择城市 */
  bindtap(e) {
    console.log(e.detail)
    var that = this;
    var where = "where.area";
    var name = e.detail.name;
    console.log(name)
    var box = wx.createAnimation({
      transformOrigin: "50% 50%",
      duration: 200,
      timingFunction: "linear",
      delay: 0
    })
    var animation = wx.createAnimation({
      transformOrigin: "50% 50%",
      duration: 200,
      timingFunction: "linear",
      delay: 0
    })

    animation.rotate(0).step();
    box.height("90rpx").step();

    that.setData({
      areaData: animation.export(),
      areaStatus: false,
      areaHidden: true,
      boxData: box.export(),
      [where]: name,
      city_name: name
    })
    console.log(that.data.where)
    wx.showLoading({
      title: '加载中...',
    })
    that.getfixedlist()
  },

  redirect: function(e) {
    console.log(e.currentTarget.dataset.url)
    var url = e.currentTarget.dataset.url;
    wx.navigateTo({
      url: url,
    })
  },
})