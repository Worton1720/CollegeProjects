package com.example.collegeapp

import android.app.Application
import com.example.collegeapp.di.authModule
import com.example.collegeapp.di.calculatorModule
import com.example.collegeapp.di.listModule
import com.example.collegeapp.di.weatherModule
import org.koin.android.ext.koin.androidContext
import org.koin.android.ext.koin.androidLogger
import org.koin.core.context.startKoin
import org.koin.core.logger.Level

class App : Application() {

    override fun onCreate() {
        super.onCreate()

        startKoin {
            androidLogger(Level.ERROR)
            androidContext(this@App)
            modules(
                weatherModule,
                calculatorModule,
                authModule,
                listModule
            )
        }
    }
}
