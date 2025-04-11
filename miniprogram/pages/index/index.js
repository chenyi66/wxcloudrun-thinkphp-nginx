const app = getApp()

Page({
    data: {
        count: 0,
        loading: false
    },

    onLoad() {
        this.getCount()
    },

    // 获取计数
    getCount() {
        this.setData({
            loading: true
        })
        app.request({
            url: '/api/count',
            method: 'GET'
        })
            .then(res => {
                this.setData({
                    count: res.data.count,
                    loading: false
                })
            })
            .catch(err => {
                console.error('获取计数失败', err)
                this.setData({
                    loading: false
                })
                wx.showToast({
                    title: '获取计数失败',
                    icon: 'none'
                })
            })
    },

    // 计数增加
    increment() {
        this.setData({
            loading: true
        })
        app.request({
            url: '/api/count',
            method: 'POST',
            data: {
                action: 'inc'
            }
        })
            .then(() => {
                this.getCount()
            })
            .catch(err => {
                console.error('计数增加失败', err)
                this.setData({
                    loading: false
                })
                wx.showToast({
                    title: '计数增加失败',
                    icon: 'none'
                })
            })
    },

    // 计数减少
    decrement() {
        this.setData({
            loading: true
        })
        app.request({
            url: '/api/count',
            method: 'POST',
            data: {
                action: 'dec'
            }
        })
            .then(() => {
                this.getCount()
            })
            .catch(err => {
                console.error('计数减少失败', err)
                this.setData({
                    loading: false
                })
                wx.showToast({
                    title: '计数减少失败',
                    icon: 'none'
                })
            })
    },

    // 清零计数
    clear() {
        this.setData({
            loading: true
        })
        app.request({
            url: '/api/count',
            method: 'POST',
            data: {
                action: 'clear'
            }
        })
            .then(() => {
                this.getCount()
            })
            .catch(err => {
                console.error('清零失败', err)
                this.setData({
                    loading: false
                })
                wx.showToast({
                    title: '清零失败',
                    icon: 'none'
                })
            })
    }
})