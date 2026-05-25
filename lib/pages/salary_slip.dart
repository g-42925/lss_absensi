import 'dart:convert';

import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';

class SalarySlipPage extends ConsumerStatefulWidget {
  const SalarySlipPage({super.key});

  @override
  ConsumerState<SalarySlipPage> createState() => _SalarySlipPageState();
}

class InfoRow extends StatelessWidget {
  final String label;
  final dynamic value;
  final bool isBold;

  const InfoRow({
    super.key,
    required this.label,
    required this.value,
    this.isBold = false,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label),
          Text(
            value,
            style: TextStyle(
              fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
            ),
          ),
        ],
      ),
    );
  }
}

class _SalarySlipPageState extends ConsumerState<SalarySlipPage> {
  DateTime? selectedDate1;
  DateTime? selectedDate2;

  DateTime? tanggalMulai;
  DateTime? tanggalSelesai;

  String formatTanggal(DateTime? date) {
    if (date == null) return "Pilih Tanggal";
    return "${date.day}/${date.month}/${date.year}";
  }

  final int year = DateTime.now().year;
  final int month = DateTime.now().month;
  final int day = DateTime.now().day;

  late Future<http.Response>? salary;

  DateTime get dt1 => DateTime(year, month, 1); // awal bulan
  DateTime get dt2 => makeSalaryDate(); // hari ini

  // bulan sebelumnya
  DateTime get from1 => DateTime(dt1.year, dt1.month - 1, 1);
  DateTime get to1 =>
      DateTime(dt1.year, dt1.month, 0); // 0 = hari terakhir bulan lalu

  // range lain
  DateTime get from2 => DateTime(dt2.year, dt2.month - 1, dt2.day);
  DateTime get to2 => dt2.subtract(const Duration(days: 1));

  DateTime get from => (day == 1) ? from1 : from2;
  DateTime get to => (day == 1) ? to1 : to2;

  String get start => from.toIso8601String().split('T').first;
  String get end => to.toIso8601String().split('T').first;

  DateTime makeSalaryDate() {
    final globalState = ref.read(globalStateProvider);
    final salaryDate = globalState.company.salaryDate;
    return DateTime(year, month, salaryDate);
  }

  Future<http.Response> getSalary() async {
    final now = DateTime.now();
    final globalState = ref.read(globalStateProvider);
    final empId = globalState.other.pegawaiId;
    final salaryDate = globalState.company.salaryDate;
    final date = DateTime(now.year, now.month, salaryDate);
    final sDate = DateFormat('yyyy-MM-dd').format(date);
    Uri url = Uri.parse("${Env.api}/api/mobile/salary/${empId}/$sDate");

    print(url);

    return http.get(url);
  }

  final formatRupiah = NumberFormat.currency(
    locale: 'id', // locale Indonesia
    symbol: 'Rp ',
    decimalDigits: 0, // jumlah angka di belakang koma
  );

  final formatAngka = NumberFormat.decimalPattern('id');

  List<Widget> makeBenefitAndPenaltyList(List<Map<String, dynamic>> list) {
    final bPList = list.map((el) {
      return InfoRow(label: el['name'], value: el['value'].toString());
    });

    return bPList.toList();
  }

  List<Widget> makeAllowanceList(List<Map<String, dynamic>> list) {
    final allowanceList = list.map((el) {
      return InfoRow(label: el['name'], value: el['value'].toString());
    });

    return allowanceList.toList();
  }

  Future<void> fetch() async {
    setState(() {
      salary = null;
    });
    setState(() {
      salary = getSalary();
    });
  }

  @override
  void initState() {
    super.initState();
    //fetch();
  }

  Widget buildInfoRow(String label, dynamic value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        children: [
          SizedBox(width: 100, child: Text("$label :")),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final company = ref.read(globalStateProvider).company;
    final other = ref.read(globalStateProvider).other;
    final args = ModalRoute.of(context)!.settings.arguments as Map;

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.all(16.0),
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Column(
                    children: [
                      Text(
                        company.name,
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(height: 4),
                      Text(company.address),
                      Divider(thickness: 1),
                    ],
                  ),
                ),
                const SizedBox(height: 8),
                Center(
                  child: Text(
                    "SLIP GAJI KARYAWAN",
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ),
                Center(
                  child: Text("Periode: ${args['tglX']} - ${args['tglY']}"),
                ),
                const SizedBox(height: 16),

                // Info Karyawan
                buildInfoRow("Nama", other.namaPegawai),
                buildInfoRow("NIK", other.pegawaiId),
                buildInfoRow("Jabatan", other.position),
                buildInfoRow("Status", other.status),

                const SizedBox(height: 16),

                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Penghasilan
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "PENGHASILAN",
                            style: TextStyle(fontWeight: FontWeight.bold),
                          ),
                          SizedBox(height: 4),
                          InfoRow(
                            label: "Gaji Pokok",
                            value: formatAngka.format(args['result']['salary']),
                          ),
                          ...makeAllowanceList(
                            List<Map<String, dynamic>>.from(
                              args['result']['allowance'].map((e) {
                                return {
                                  'name': e['name'],
                                  'value': formatAngka.format(e['value']),
                                };
                              }),
                            ),
                          ),
                          Divider(),
                          InfoRow(
                            label: "TOTAL (A)",
                            value: formatAngka.format(
                              args['result']['totalIncome'],
                            ),
                            isBold: true,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 16),
                    // Potongan
                  ],
                ),
                const SizedBox(height: 16),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Penghasilan
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "POTONGAN",
                            style: TextStyle(fontWeight: FontWeight.bold),
                          ),
                          SizedBox(height: 4),
                          ...makeBenefitAndPenaltyList(
                            List<Map<String, dynamic>>.from(
                              args['result']['benefit'].map((e) {
                                return {
                                  'name': e['name'],
                                  'value': formatAngka.format(e['value']),
                                };
                              }),
                            ),
                          ),
                          ...makeBenefitAndPenaltyList(
                            List<Map<String, dynamic>>.from(
                              args['result']['penalty'].map((e) {
                                return {
                                  'name': e['name'],
                                  'value': formatAngka.format(e['value']),
                                };
                              }),
                            ),
                          ),
                          Divider(),
                          InfoRow(
                            label: "TOTAL (B)",
                            value: formatAngka.format(
                              args['result']['totalBenefit'],
                            ),
                            isBold: true,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 16),
                    // Potongan
                  ],
                ),

                const SizedBox(height: 16),

                // Penerimaan bersih
                Text(
                  "PENERIMAAN BERSIH (A-B)   = Rp ${formatAngka.format(args['result']['thp'])}",
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
