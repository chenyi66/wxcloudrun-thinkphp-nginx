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
        selectedTimeSlots: [],
        mainServices: [
            {
                name: '小儿推拿',
                subServices: ['小儿推拿']
            },
            {
                name: '产后康复',
                subServices: ['子宫复旧', '盆底肌', '骨盆', '腹直肌', '形体塑形', '卵巢保养', '脏器养护', '危险修复', '产后发汗', '古法搓浴']
            },
            {
                name: '乳腺',
                subServices: ['开奶', '乳腺疏通', '乳腺炎', '催乳', '科学回奶', '排残奶']
            },
            {
                name: '成人理疗',
                subServices: ['头疗', '全息理疗', '面部按摩', '乳腺疏通', '腰背', '肩背', '臂部', '手臂', '太极刮痧', '艾灸刮痧', '手工拔罐', '随身灸', '穴位艾灸', '私密艾灸', '隔姜灸', '三伏贴', '三伏灸', '督脉灸', '拔罐', '走罐', '滑罐', '拔罐减肥']
            }
        ],
        selectedMainService: '',
        selectedSubServices: [],
        currentSubServices: [], // 添加当前选中主服务的子服务列表
        formData: {
            name: '',
            phone: '',
            age: '',
            services: []
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
            selectedTimeSlots: [],
            canSubmit: false
        })

        this.getTimeSlots(selectedDate)
    },

    // 选择时间段
    selectTimeSlot(e) {
        const selectedTime = e.currentTarget.dataset.time
        const selectedIndex = parseInt(e.currentTarget.dataset.index)
        const timeSlots = [...this.data.timeSlots]
        const selectedTimeSlots = [...this.data.selectedTimeSlots]

        // 切换选中状态
        timeSlots[selectedIndex].selected = !timeSlots[selectedIndex].selected

        if (timeSlots[selectedIndex].selected) {
            selectedTimeSlots.push(selectedTime)
        } else {
            const index = selectedTimeSlots.indexOf(selectedTime)
            if (index > -1) {
                selectedTimeSlots.splice(index, 1)
            }
        }

        this.setData({
            timeSlots,
            selectedTimeSlots
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

    selectMainService(e) {
        const mainServiceName = e.currentTarget.dataset.name
        const mainService = this.data.mainServices.find(service => service.name === mainServiceName)

        this.setData({
            selectedMainService: mainServiceName,
            currentSubServices: mainService ? mainService.subServices : [] // 更新当前子服务列表
        })
    },

    selectSubService(e) {
        const selectedService = e.currentTarget.dataset.name
        let selectedSubServices = [...this.data.selectedSubServices]
        const selectedTimeSlots = this.data.selectedTimeSlots

        // 如果还没有选择时间段，提示用户先选择时间段
        if (selectedTimeSlots.length === 0) {
            wx.showToast({
                title: '请先选择时间段',
                icon: 'none'
            })
            return
        }

        // 检查是否是取消选择
        const index = selectedSubServices.indexOf(selectedService)
        if (index > -1) {
            // 取消选择
            selectedSubServices.splice(index, 1)
            this.setData({
                selectedSubServices,
                'formData.services': selectedSubServices
            })
        } else {
            // 检查是否还有可选择的时间段
            if (selectedSubServices.length >= selectedTimeSlots.length) {
                wx.showToast({
                    title: '已选择所有时间段对应的项目',
                    icon: 'none'
                })
                return
            }
            // 添加新选择的项目
            selectedSubServices.push(selectedService)
            this.setData({
                selectedSubServices,
                'formData.services': selectedSubServices
            })
        }

        // 更新用户界面提示，显示已选择的时间段和项目对应关系
        let message = '当前预约：\n'
        selectedSubServices.forEach((service, idx) => {
            const timeSlot = selectedTimeSlots[idx]
            message += `${timeSlot} → ${service}\n`
        })

        // 如果还有未选择项目的时间段，提示用户继续选择
        if (selectedSubServices.length < selectedTimeSlots.length) {
            const nextTime = selectedTimeSlots[selectedSubServices.length]
            message += `\n请为 ${nextTime} 选择项目\n`
            message += '(可从任意大项中选择小项)'
        }

        wx.showModal({
            title: '预约项目',
            content: message,
            showCancel: false,
            confirmText: '确定'
        })

        this.checkCanSubmit()
    },

    // 检查是否可以提交
    checkCanSubmit() {
        const { name, phone, age, services } = this.data.formData
        const phoneRegex = /^1[3-9]\d{9}$/
        const isPhoneValid = phoneRegex.test(phone)

        // 验证时间段和项目数量是否相等
        const isTimeServiceMatch = services.length === this.data.selectedTimeSlots.length

        const canSubmit = name && isPhoneValid && age && services.length > 0 &&
            this.data.selectedDate && this.data.selectedTimeSlots.length > 0 && isTimeServiceMatch

        this.setData({ canSubmit })

        if (phone && !isPhoneValid) {
            wx.showToast({
                title: '请输入正确的手机号',
                icon: 'none'
            })
        }

        if (services.length > 0 && !isTimeServiceMatch) {
            wx.showToast({
                title: '时间段和项目数量必须相同',
                icon: 'none'
            })
        }
    },

    // 提交预约
    submitAppointment() {
        if (!this.data.canSubmit) return

        // 组合每个时间段和服务项目的一一对应关系
        const appointments = [];
        this.data.selectedTimeSlots.forEach((time, index) => {
            appointments.push({
                date: this.data.selectedDate,
                time: time,
                name: this.data.formData.name,
                phone: this.data.formData.phone,
                age: this.data.formData.age,
                service: this.data.formData.services[index]
            })
        })

        const appointmentData = {
            appointments: appointments
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
                    selectedTimeSlots: [],
                    formData: {
                        name: '',
                        phone: '',
                        age: '',
                        services: []
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