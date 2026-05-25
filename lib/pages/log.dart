import 'package:intl/intl.dart';
import 'dart:convert';
import 'package:camera/camera.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/global_state.dart';
import '../env/env.dart';
import 'dart:async';

class  LogPage extends ConsumerStatefulWidget{
  const LogPage({super.key});

  @override
  ConsumerState<LogPage> createState() => _LogPageState();
}

class _LogPageState extends ConsumerState<LogPage>{
  late Future<http.Response>? log;
  
  Future<http.Response> getLog() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/log/${other.pegawaiId}");

    var response = http.get(url);

    try {
      await response;
    } 
    catch (e) {
      print(e);
    }

    return response;
  }

  Future<void> fetch() async {
    setState(() {
      log = null;
    });
    setState(() {
      log = getLog();
    });
  }

  @override
  void initState() {
    super.initState();
    fetch();
  }

  @override Widget build(BuildContext ctx){
    return Scaffold(
      body:SafeArea(
				child:SingleChildScrollView(
					child:Padding(
						padding: EdgeInsets.all(16.0),
						child:FutureBuilder(
							future:log,
							builder:(context,snapshot){
								if(snapshot.connectionState == ConnectionState.waiting) {
									return Center(child: CircularProgressIndicator());
								} 
								if (snapshot.hasError) {
									return Center(child: Text("something went wrong"));
								}
								else{
									final response = snapshot.data!;
									final data = jsonDecode(response.body);
									final List list = data['r'];
									final filter = DateFormat('yyyy-MM-dd').format(DateTime.now());
									final filtered = list.where((item) {
										return item['tanggal_absen'] == filter;
									}).toList();

									return Column(
										children:[
											Table(
												border: TableBorder.all(),
												children: [
													TableRow(
														children: [
															Padding(
																padding: EdgeInsets.all(8),
																child: Text('Tanggal'),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text('Jam masuk'),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text('Jam pulang'),
															)
														]
													),
													...filtered.map((d){
														return TableRow(children: [
															Padding(
																padding: EdgeInsets.all(8),
																child: Text(d['tanggal_absen']),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text(d['jam_masuk']),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text(d['jam_keluar']),
															),
														]);
													}).toList(),
												],
											),
										  SizedBox(height:10),
											Table(
												border: TableBorder.all(),
												children: [
													TableRow(
														children: [
															Padding(
																padding: EdgeInsets.all(8),
																child: Text('Tanggal'),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text('Jam masuk'),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text('Jam pulang'),
															)
														]
													),
													...data['r'].map((d){
														return TableRow(children: [
															Padding(
																padding: EdgeInsets.all(8),
																child: Text(d['tanggal_absen']),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text(d['jam_masuk']),
															),
															Padding(
																padding: EdgeInsets.all(8),
																child: Text(d['jam_keluar']),
															),
														]);
													}).toList(),
												],
											)
										]
									);
								}          
							}
						)
				  )
				)
			)
    );
  }
}