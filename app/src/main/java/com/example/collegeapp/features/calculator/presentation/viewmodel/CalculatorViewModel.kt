
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.collegeapp.R
import com.example.collegeapp.features.calculator.domain.model.CalculationError
import com.example.collegeapp.features.calculator.domain.model.CalculationResult
import com.example.collegeapp.features.calculator.domain.usecase.CalculateExpressionUseCase
import com.example.collegeapp.features.calculator.presentation.state.CalculatorState

/**
 * ViewModel для калькулятора
 * Управляет состоянием UI и выполняет вычисления через UseCase
 */
class CalculatorViewModel(
    private val calculateExpressionUseCase: CalculateExpressionUseCase
) : ViewModel() {

    private val _state = MutableLiveData(CalculatorState())
    val state: LiveData<CalculatorState> = _state

    /**
     * Добавить символ к текущему выражению
     */
    fun onInput(input: String) {
        val currentState = _state.value ?: CalculatorState()

        // Если предыдущее состояние было ошибкой, очищаем
        if (currentState.isError) {
            _state.value = CalculatorState(
                displayText = input,
                currentExpression = input
            )
            return
        }

        val newExpression = if (currentState.displayText == "0") {
            input
        } else {
            currentState.currentExpression + input
        }

        _state.value = currentState.copy(
            displayText = newExpression,
            currentExpression = newExpression
        )
    }

    /**
     * Очистить калькулятор
     */
    fun onClear() {
        _state.value = CalculatorState()
    }

    /**
     * Вычислить текущее выражение
     */
    fun onCalculate(
        decimalFormat: String,
        errorEmptyExpression: String,
        errorDivisionByZero: String,
        errorInvalidExpression: String,
        errorCalculationFailed: String,
        errorUnknown: String
    ) {
        val currentState = _state.value ?: return
        val expression = currentState.currentExpression

        if (expression.isBlank()) {
            return
        }

        when (val result = calculateExpressionUseCase(expression, decimalFormat)) {
            is CalculationResult.Success -> {
                _state.value = currentState.copy(
                    displayText = result.result,
                    currentExpression = result.result,
                    isError = false
                )
            }
            is CalculationResult.Error -> {
                _state.value = currentState.copy(
                    displayText = mapErrorToString(
                        result.error,
                        result.message,
                        errorEmptyExpression,
                        errorDivisionByZero,
                        errorInvalidExpression,
                        errorCalculationFailed,
                        errorUnknown
                    ),
                    isError = true
                )
            }
        }
    }

    private fun mapErrorToString(
        error: CalculationError,
        message: String?,
        errorEmptyExpression: String,
        errorDivisionByZero: String,
        errorInvalidExpression: String,
        errorCalculationFailed: String,
        errorUnknown: String
    ): String {
        return when (error) {
            CalculationError.EMPTY_EXPRESSION -> errorEmptyExpression
            CalculationError.DIVISION_BY_ZERO -> errorDivisionByZero
            CalculationError.INVALID_EXPRESSION -> errorInvalidExpression
            CalculationError.CALCULATION_FAILED -> String.format(errorCalculationFailed, message ?: "")
            CalculationError.UNKNOWN -> String.format(errorUnknown, message ?: "")
        }
    }
}
