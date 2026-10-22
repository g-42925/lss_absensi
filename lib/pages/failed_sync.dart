import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import '../providers/global_state.dart';
import '../env/env.dart';

class FailedSyncPage extends ConsumerStatefulWidget {
  const FailedSyncPage({super.key});

  @override
  ConsumerState<FailedSyncPage> createState() => _FailedSyncPageState();
}

class _FailedSyncPageState extends ConsumerState<FailedSyncPage> {
  SupabaseClient supabase = Supabase.instance.client;

  bool _isSyncing = false;

  Future<void> _manualSync() async {
    setState(() => _isSyncing = true);

    String employeeId = ref.read(globalStateProvider).other.pegawaiId;

    try {
      await supabase.from('sessions').delete().eq('employee_id', employeeId);
      Navigator.pushReplacementNamed(context, '/login');
    } catch (e) {
      setState(() => _isSyncing = false);

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Sinkronisasi manual gagal'),
          duration: Duration(seconds: 2),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      body: SafeArea(
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Tombol bulat
              GestureDetector(
                onTap: _isSyncing ? null : _manualSync,
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  width: 100,
                  height: 100,
                  decoration: BoxDecoration(
                    color: _isSyncing ? Colors.blue[200] : Colors.blue,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.blue.withOpacity(0.4),
                        blurRadius: 12,
                        offset: const Offset(0, 6),
                      ),
                    ],
                  ),
                  child: Center(
                    child: _isSyncing
                        ? const SizedBox(
                            width: 30,
                            height: 30,
                            child: CircularProgressIndicator(
                              strokeWidth: 3,
                              color: Colors.white,
                            ),
                          )
                        : const Icon(Icons.sync, size: 40, color: Colors.white),
                  ),
                ),
              ),

              const SizedBox(height: 24),

              // Penjelasan fungsi tombol
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 32),
                child: Text(
                  'Proses logout otomatis sudah gagal, klik tombol utk mengulangi proses logout',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 16,
                    color: Colors.black87,
                    height: 1.4,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
