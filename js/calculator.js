
document.addEventListener('DOMContentLoaded', () => {
    const calcForm = document.getElementById('macro-form');
    const resultsPanel = document.getElementById('results');
    const resetBtn = document.getElementById('reset-calc');

    // DOM Elements for results
    const resCalories = document.getElementById('res-calories');
    const resProtein = document.getElementById('res-protein');
    const resCarbs = document.getElementById('res-carbs');
    const resFats = document.getElementById('res-fats');

    /**
     * Validates an input field and toggles error classes
     * @param {HTMLInputElement} input 
     * @returns {boolean}
     */
    const validateField = (input) => {
        const isValid = input.checkValidity();
        const parent = input.parentElement;
        
        if (!isValid) {
            parent.classList.add('invalid');
        } else {
            parent.classList.remove('invalid');
        }
        return isValid;
    };

    // Real-time validation on input
    calcForm.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => validateField(input));
    });

    /**
     * Handles the form submission and calculation logic
     * @param {Event} e 
     */
    const handleCalculate = (e) => {
        e.preventDefault();

        // Validate all fields first
        const inputs = calcForm.querySelectorAll('input');
        let formValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) formValid = false;
        });

        if (!formValid) return;

        // Extract values
        const gender = document.getElementById('gender').value;
        const age = parseInt(document.getElementById('age').value);
        const weight = parseFloat(document.getElementById('weight').value);
        const height = parseFloat(document.getElementById('height').value);
        const activity = parseFloat(document.getElementById('activity').value);
        const goal = document.getElementById('goal').value;

        // Calculate BMR (Mifflin-St Jeor)
        let bmr;
        if (gender === 'male') {
            bmr = (10 * weight) + (6.25 * height) - (5 * age) + 5;
        } else {
            bmr = (10 * weight) + (6.25 * height) - (5 * age) - 161;
        }

        // Calculate TDEE
        let tdee = bmr * activity;

        if (goal === 'lose') tdee -= 500;
        if (goal === 'gain') tdee += 300;

        // Calculate Macro Splits 
        const protein = (tdee * 0.30) / 4;
        const carbs = (tdee * 0.40) / 4;
        const fats = (tdee * 0.30) / 9;

        // Update DOM
        resCalories.textContent = Math.round(tdee);
        resProtein.textContent = Math.round(protein);
        resCarbs.textContent = Math.round(carbs);
        resFats.textContent = Math.round(fats);

        // Save to localStorage for Activity 3 
        const userMacros = {
            calories: Math.round(tdee),
            protein: Math.round(protein),
            carbs: Math.round(carbs),
            fats: Math.round(fats)
        };
        localStorage.setItem('athleticEatsMacros', JSON.stringify(userMacros));

        // Show results panel with smooth transition
        resultsPanel.style.display = 'block';
        resultsPanel.scrollIntoView({ behavior: 'smooth' });
    };

    // Resets the form and hides results
    const handleReset = () => {
        calcForm.reset();
        resultsPanel.style.display = 'none';
        calcForm.querySelectorAll('.form-group').forEach(group => group.classList.remove('invalid'));
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Event Listeners
    calcForm.addEventListener('submit', handleCalculate);
    resetBtn.addEventListener('click', handleReset);
});
