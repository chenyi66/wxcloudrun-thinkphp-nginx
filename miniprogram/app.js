App({
    globalData: {
        // baseUrl: 'http://localhost:27081',
        baseUrl: 'https://thinkphp-nginx-5cpb-151538-4-1352299247.sh.run.tcloudbase.com',
        userInfo: null
    },

    onLaunch() {
        // 展示本地存储能力
        const logs = wx.getStorageSync('logs') || []
        logs.unshift(Date.now())
        wx.setStorageSync('logs', logs)
    },

    // 请求封装
    request(options) {
        return new Promise((resolve, reject) => {
            wx.request({
                url: this.globalData.baseUrl + options.url,
                method: options.method || 'GET',
                data: options.data,
                header: {
                    'content-type': 'application/json'
                },
                success: (res) => {
                    console.log('API Response:', res)
                    // 确保返回的数据结构包含必要的字段
                    const response = {
                        code: res.statusCode === 200 ? 0 : res.statusCode,
                        data: res.data,
                        message: res.data?.message || ''
                    }
                    resolve(response)
                },
                fail: (err) => {
                    console.error('Request failed:', err)
                    reject(err)
                }
            })
        })
    }
})