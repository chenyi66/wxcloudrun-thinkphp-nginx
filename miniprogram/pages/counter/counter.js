const app = getApp()

Page({
    data: {
        weekDays: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
        dateList: [],
        timeSlots: [
            { time: '08:30', capacity: 2, available: true },
            { time: '09:40', capacity: 2, available: true },
            { time: '10:50', capacity: 2, available: true },
            { time: '14:30', capacity: 2, available: true },
            { time: '15:50', capacity: 2, available: true },
            { time: '17:30', capacity: 2, available: true },
            { time: '18:50', capacity: 2, available: true },
            { time: '20:00', capacity: 2, available: true }
        ],
        selectedDate: '',
        selectedTimeSlot: '',
        formData: {
            name: '',
            phone: '',
            age: '',
            service: ''
        },
        canSubmit: false
    },

    onLoad() {
        this.initDateList()
    },

    // 初始化日期列表
    initDateList() {
        const today = new Date()
        const dateList = []

        for (let i = 0; i < 14; i++) {
            const date = new Date(today)
            date.setDate(today.getDate() + i)

            dateList.push({
                date: this.formatDate(date),
                day: date.getDate(),
                available: true, // 默认可预约，选择日期时再检查具体时间段
                selected: false
            })
        }

        this.setData({ dateList })
    },

    // 获取指定日期的可预约时间段
    getTimeSlots(date) {
        app.request({
            url: '/api/appointment/getTimeSlots',
            method: 'GET',
            data: { date }
        }).then(res => {
            console.log('getTimeSlots response:', res)
            if (res.data.code === 0) {
                this.setData({
                    timeSlots: res.data.data || []
                })
            } else {
                wx.showToast({
                    title: res.message || '获取时间段失败',
                    icon: 'none'
                })
            }
        }).catch(err => {
            console.error('获取时间段失败', err)
            wx.showToast({
                title: '获取时间段失败，请重试',
                icon: 'none'
            })
        })
    },

    // 格式化日期
    formatDate(date) {
        const year = date.getFullYear()
        const month = (date.getMonth() + 1).toString().padStart(2, '0')
        const day = date.getDate().toString().padStart(2, '0')
        return `${year}-${month}-${day}`
    },

    selectDate(e) {
        const selectedDate = e.currentTarget.dataset.date
        const dateList = this.data.dateList.map(item => ({
            ...item,
            selected: item.date === selectedDate
        }))

        this.setData({
            dateList,
            selectedDate,
            timeSlots: [],
            selectedTimeSlot: '',
            canSubmit: false
        })

        this.getTimeSlots(selectedDate)
    },

    // 选择时间段
    selectTimeSlot(e) {
        const selectedTime = e.currentTarget.dataset.time
        const timeSlots = this.data.timeSlots.map(item => ({
            ...item,
            selected: item.time === selectedTime
        }))

        this.setData({
            timeSlots,
            selectedTimeSlot: selectedTime
        })

        this.checkCanSubmit()
    },

    // 选择服务项目
    // 处理输入框变化
    handleInput(e) {
        const { field } = e.currentTarget.dataset;
        this.setData({
            [`formData.${field}`]: e.detail.value
        });
        this.checkCanSubmit();
    },

    selectService(e) {
        this.setData({
            'formData.service': e.detail.value
        })
        this.checkCanSubmit()
    },

    // 检查是否可以提交
    checkCanSubmit() {
        const { name, phone, age, service } = this.data.formData
        const phoneRegex = /^1[3-9]\d{9}$/
        const isPhoneValid = phoneRegex.test(phone)
        const canSubmit = name && isPhoneValid && age && service &&
            this.data.selectedDate && this.data.selectedTimeSlot

        this.setData({ canSubmit })

        if (phone && !isPhoneValid) {
            wx.showToast({
                title: '请输入正确的手机号',
                icon: 'none'
            })
        }
    },

    // 提交预约
    submitAppointment() {
        if (!this.data.canSubmit) return

        const appointmentData = {
            date: this.data.selectedDate,
            time: this.data.selectedTimeSlot,
            ...this.data.formData
        }

        app.request({
            url: '/api/appointment/submit',
            method: 'POST',
            data: appointmentData
        }).then(res => {
            if (res.data.code === 0) {
                wx.showToast({
                    title: '预约成功',
                    icon: 'success'
                })
                // 重置表单
                this.setData({
                    selectedDate: '',
                    selectedTimeSlot: '',
                    formData: {
                        name: '',
                        phone: '',
                        age: '',
                        service: ''
                    },
                    canSubmit: false
                })
                this.initDateList()
            } else {
                wx.showToast({
                    title: res.data.message || '预约失败，请重试',
                    icon: 'none'
                })
            }
        }).catch(err => {
            console.error('预约失败', err)
            wx.showToast({
                title: '预约失败，请重试',
                icon: 'none'
            })
        })
    }
})