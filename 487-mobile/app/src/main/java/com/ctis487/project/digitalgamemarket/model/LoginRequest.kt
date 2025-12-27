package com.ctis487.project.digitalgamemarket.model

import androidx.databinding.BaseObservable
import androidx.databinding.Bindable
import com.ctis487.project.digitalgamemarket.BR

class LoginRequest : BaseObservable(){
    @get:Bindable
    var username: String = ""
        set(value) {
            field = value
            notifyPropertyChanged(BR.username)
        }
}