package com.example.collegeapp.di

import androidx.room.Room
import com.example.collegeapp.features.auth.data.local.database.AppDatabase
import com.example.collegeapp.features.auth.data.repository.UserRepositoryImpl
import com.example.collegeapp.features.auth.domain.repository.UserRepository
import com.example.collegeapp.features.auth.presentation.viewmodel.LoginViewModel
import com.example.collegeapp.features.auth.presentation.viewmodel.RegisterViewModel
import org.koin.android.ext.koin.androidApplication
import org.koin.androidx.viewmodel.dsl.viewModelOf
import org.koin.dsl.module

val authModule = module {
    single {
        Room.databaseBuilder(
            androidApplication(),
            AppDatabase::class.java,
            "app_database"
        ).fallbackToDestructiveMigration().build()
    }

    single { get<AppDatabase>().userDao() }
    single { get<AppDatabase>().favoriteCityDao() }

    single<UserRepository> { UserRepositoryImpl(get(), get()) }

    viewModelOf(::RegisterViewModel)
    viewModelOf(::LoginViewModel)
}
