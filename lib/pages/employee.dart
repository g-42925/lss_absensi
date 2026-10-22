import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class EmployeePage extends ConsumerStatefulWidget {
  const EmployeePage({super.key});

  @override
  ConsumerState<EmployeePage> createState() => _EmployeePageState();
}

class _EmployeePageState extends ConsumerState<EmployeePage> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(appBar: AppBar(title: const Text("Karyawan")));
  }
}
