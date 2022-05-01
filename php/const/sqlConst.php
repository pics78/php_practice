<?php

  // db table and columns
  define('TNAME', 'employee_info');
  define('NO', 'emp_no');
  define('PASS', 'pass');
  define('NAME', 'emp_name');
  define('DEPT_ID', 'dept_id'); // MUL -> department_master
  define('AUTHFLG', 'auth_flg');
  define('ENT_YEAR', 'ent_year');
  define('ENT_MON', 'ent_mon');
  define('ENT_DAY', 'ent_day');
  define('LEV_YEAR', 'lev_year');
  define('LEV_MON', 'lev_mon');
  define('LEV_DAY', 'lev_day');
  define('ADDRESS', 'address');

  define('DEPT_MST', 'department_master');
  // define('DEPT_ID', 'dept_id');
  define('DEPT_NAME', 'dept_name');

  define('EVAL_INFO', 'evaluation_info');
  // define('NO', 'emp_no');
  define('EVAL_YEAR', 'eval_year');
  define('GOAL', 'goal');
  define('FST_RES', 'first_result');
  define('SCD_RES', 'second_result');
  define('TOTAL_RES', 'total_result');
  define('FST_EVAL', 'first_eval');
  define('FST_EVALUATOR', 'first_evaluator');
  define('SCD_EVAL', 'second_eval');
  define('SCD_EVALUATOR', 'second_evaluator');
  define('TOTAL_EVAL', 'total_eval');
  define('TOTAL_EVALUATOR', 'total_evaluator');

  define('AUTH_MST', 'auth_master');
  define('AUTH_ID', 'auth_id');
  define('AUTH_NAME', 'auth_name');

  // auth flg definition
  define('FLG_MGR', 0); // manager
  define('FLG_EVR', 1); // evaluator
  define('FLG_MBR', 2); // mamber

?>
