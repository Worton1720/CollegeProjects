package com.example.collegeapp.di

import com.example.collegeapp.features.list.presentation.viewmodel.ListViewModel
import org.koin.androidx.viewmodel.dsl.viewModelOf
import org.koin.dsl.module

val listModule = module {
    viewModelOf(::ListViewModel)
}
