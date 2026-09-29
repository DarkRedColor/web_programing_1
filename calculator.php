<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle   = 'Calculator';
$currentPage = 'calculator.php';

require __DIR__ . '/includes/header.php';
?>
<h1>My calculator</h1>
<table id="calculator_table" >
    <tr>
        <td>First number</td>
        <td>Second Number</td>
        <td>Result</td>
    </tr>
    <tr >
        <td><input name="number1" type="number" size=10 id='num1'/></td>
        <td><input name="number2" type="number" size=10 id='num2'/></td>
        <td> <input type="text" id='result' readonly ></td>
    </tr>
    <tr>
        <td colspan="3">
            <button onclick="showresult('1')">+</button>
            <button onclick="showresult('2')">-</button>
            <button onclick="showresult('3')">*</button>
            <button onclick="showresult('4')">/</button>
            <button onclick="showresult('5')">%</button>
        </td>
    </tr>
</table>
<script src="assets/calculator.js"></script>