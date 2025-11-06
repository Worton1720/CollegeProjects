package com.example.collegeapp.di

import com.example.collegeapp.features.calculator.data.repository.CalculatorRepositoryImpl
import com.example.collegeapp.features.calculator.domain.repository.CalculatorRepository
import com.example.collegeapp.features.calculator.domain.usecase.CalculateExpressionUseCase
import com.example.collegeapp.features.calculator.presentation.viewmodel.CalculatorViewModel
import org.koin.androidx.viewmodel.dsl.viewModel
import org.koin.dsl.module

/**
 * Koin модуль для фичи Calculator
 * Содержит все зависимости для калькулятора: Repository, UseCase, ViewModel
 */
val calculatorModule = module {

    // CalculatorRepository
    single<CalculatorRepository> {
        CalculatorRepositoryImpl()
    }

    // CalculateExpressionUseCase
    factory {
        CalculateExpressionUseCase(get())
    }

    // CalculatorViewModel
    viewModel {
        CalculatorViewModel(get())
    }
}
