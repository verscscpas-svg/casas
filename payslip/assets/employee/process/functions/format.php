<?php

function formatTIN($tin)
{
    $tin = preg_replace('/[^0-9]/', '', $tin);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{5})/', '$1-$2-$3-$4', $tin);
}

function formatSSS($sss)
{
    $sss = preg_replace('/[^0-9]/', '', $sss);
    return preg_replace('/(\d{2})(\d{7})(\d{1})/', '$1-$2-$3', $sss);
}

function formatPhilHealth($ph)
{
    $ph = preg_replace('/[^0-9]/', '', $ph);
    return preg_replace('/(\d{2})(\d{9})(\d{1})/', '$1-$2-$3', $ph);
}

function formatPagIbig($pagibig)
{
    $pagibig = preg_replace('/[^0-9]/', '', $pagibig);
    return preg_replace('/(\d{4})(\d{4})(\d{4})/', '$1-$2-$3', $pagibig);
}
