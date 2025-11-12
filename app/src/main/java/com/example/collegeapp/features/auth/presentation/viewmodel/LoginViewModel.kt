package com.example.collegeapp.features.auth.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.collegeapp.features.auth.domain.repository.UserRepository
import kotlinx.coroutines.launch

class LoginViewModel(private val userRepository: UserRepository) : ViewModel() {

    private val _loginSuccess = MutableLiveData<Boolean>()
    val loginSuccess: LiveData<Boolean> = _loginSuccess

    fun authenticateUser(username: String, passwordHash: String) {
        viewModelScope.launch {
            _loginSuccess.value = userRepository.authenticateUser(username, passwordHash)
        }
    }
}
