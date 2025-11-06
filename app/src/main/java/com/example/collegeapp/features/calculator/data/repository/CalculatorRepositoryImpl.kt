package com.example.collegeapp.features.calculator.data.repository

import com.example.collegeapp.features.calculator.domain.model.CalculationResult
import com.example.collegeapp.features.calculator.domain.repository.CalculatorRepository
import net.objecthunter.exp4j.ExpressionBuilder
import java.text.DecimalFormat

/**
 * Реализация репозитория калькулятора (Data слой)
 * Использует библиотеку exp4j для вычисления математических выражений
 */
class CalculatorRepositoryImpl : CalculatorRepository {

    private val decimalFormat = DecimalFormat("#.##########")

    override fun calculate(expression: String): CalculationResult {
        return try {
            // Заменяем символы × и ÷ на * и /
            val normalizedExpression = expression
                .replace("×", "*")
                .replace("÷", "/")
                .replace(",", ".")

            // Проверка на пустое выражение после нормализации
            if (normalizedExpression.isBlank()) {
                return CalculationResult.Error("Выражение пустое")
            }

            // Создаём и вычисляем выражение с помощью exp4j
            val exp = ExpressionBuilder(normalizedExpression).build()
            val result = exp.evaluate()

            // Проверка на бесконечность и NaN
            when {
                result.isInfinite() -> CalculationResult.Error("Деление на ноль")
                result.isNaN() -> CalculationResult.Error("Некорректное выражение")
                else -> CalculationResult.Success(decimalFormat.format(result))
            }
        } catch (e: ArithmeticException) {
            CalculationResult.Error("Ошибка вычисления: ${e.message}")
        } catch (e: IllegalArgumentException) {
            CalculationResult.Error("Некорректное выражение")
        } catch (e: Exception) {
            CalculationResult.Error("Неизвестная ошибка: ${e.message}")
        }
    }
}
