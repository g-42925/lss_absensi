import 'package:absensi/pages/log.dart';
import 'package:absensi/pages/activity.dart';
import 'package:absensi/pages/break.dart';
import 'package:absensi/pages/break_end.dart';
import 'package:absensi/pages/claim.dart';
import 'package:absensi/pages/claim_submit.dart';
import 'package:absensi/pages/done_task.dart';
import 'package:absensi/pages/employees.dart';
import 'package:absensi/pages/exception.dart';
import 'package:absensi/pages/exception_add.dart';
import 'package:absensi/pages/exception_edit.dart';
import 'package:absensi/pages/failed_sync.dart';
import 'package:absensi/pages/half_leave.dart';
import 'package:absensi/pages/leave.dart';
import 'package:absensi/pages/leave_apply.dart';
import 'package:absensi/pages/notification.dart';
import 'package:absensi/pages/overwork.dart';
import 'package:absensi/pages/overwork_add.dart';
import 'package:absensi/pages/overwork_end.dart';
import 'package:absensi/pages/overwork_start.dart';
import 'package:absensi/pages/permission_success.dart';
import 'package:absensi/pages/salary.dart';
import 'package:absensi/pages/salary_slip.dart';
import 'package:absensi/pages/schedule.dart';
import 'package:absensi/pages/task.dart';
import 'package:absensi/pages/task_add.dart';
import 'package:absensi/pages/task_edit.dart';
import 'package:absensi/pages/task_end.dart';
import 'package:absensi/pages/task_filter.dart';
import 'package:absensi/pages/task_start.dart';
import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:geolocator/geolocator.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hydrated_riverpod/hydrated_riverpod.dart';
import 'package:path_provider/path_provider.dart';
import './env/env.dart';
import './pages/home.dart';
import './pages/login.dart';
import './pages/signin.dart';
import './pages/signout.dart';
import './pages/permission.dart';
import './pages/permission_handle.dart';
import './pages/short_permission.dart';
import './pages/calendar.dart';
import './pages/long_permission.dart';
import 'dart:math';
import 'package:audioplayers/audioplayers.dart';
import '../providers/global_state.dart';
import 'package:shared_preferences/shared_preferences.dart';

const int CURRENT_VERSION = 47; // naikkan setiap release

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  final cameras = await availableCameras();

  final storageDirectory = await getApplicationDocumentsDirectory();


  final storage = await HydratedStorage.build(
    storageDirectory: storageDirectory,
  );

	final prefs = await SharedPreferences.getInstance();
  final lastVersion = prefs.getInt('app_version') ?? 0;

  await Supabase.initialize(url: Env.supabaseUrl, anonKey: Env.supabaseKey);

  final camera = cameras.firstWhere(
    (cam) => cam.lensDirection == CameraLensDirection.front,
  );

	if (lastVersion < CURRENT_VERSION) {
    await storage.clear();     
		prefs.setInt('app_version', CURRENT_VERSION);
  }

  HydratedRiverpod.initialize(storage: storage);

	try {
    runApp(
      ProviderScope(
        child: MyApp(camera: camera)
      )
    );
  } 
	catch(e) {
    runApp(
      MaterialApp(
        home: Center(
          child: Text(
            'Terjadi kesalahan, silakan keluar daro aplikasi'
          )
        )
      )
    );
  }


  runApp(
    ProviderScope(
      child: MyApp(camera: camera)
    )
  );
}

class MyApp extends ConsumerStatefulWidget {
  final CameraDescription camera;

	const MyApp({super.key, required this.camera});

  @override ConsumerState<MyApp> createState() => _MyAppState();

}

class _MyAppState extends ConsumerState<MyApp>{
  late Future<List<dynamic>> _future;
  double latitude = 0;
  double longitude = 0;


	Future<Map<String, double>> getLocation(BuildContext context) async {
    try {
      Position position = await Geolocator.getCurrentPosition();
      return {'lat': position.latitude, 'lon': position.longitude};
    } 
    catch (e) {
      await Geolocator.requestPermission();
      Position position = await Geolocator.getCurrentPosition();
      return {'lat': position.latitude, 'lon': position.longitude};
    }
  }


	@override
  void initState() {
    super.initState();
    // _future = Future.wait([
    //   requestLocation(),
    // ])
    // .then((value) {
    //   latitude = value[0].latitude;
    //   longitude = value[0].longitude;
    //   return value;
    // });
  }

	Map<String, WidgetBuilder> createRoute(BuildContext context) {
    return {
      '/': (_) => MyHomePage(),
      '/log': (_) => LogPage(),
      '/half_leave': (_) => HalfLeavePage(),
      '/claim': (_) => ClaimPage(),
      '/make_task': (_) => TaskAddPage(),
      '/failed_sync': (_) => FailedSyncPage(),
      '/task_edit': (_) => TaskEditPage(),
      '/exception_edit': (_) => ExceptionEditPage(),
      '/task_start': (_) =>
          TaskStartPage(camera: widget.camera, coord: getLocation(context)),
      '/task_end': (_) =>
          TaskEndPage(camera: widget.camera, coord: getLocation(context)),
      '/task': (_) => TaskPage(camera: widget.camera, coord: getLocation(context)),
      '/task_filter': (_) =>
          TaskFilterPage(camera: widget.camera, coord: getLocation(context)),
      '/done_task': (_) =>
          DoneTaskPage(camera: widget.camera, coord: getLocation(context)),

      '/employees': (_) => EmployeesPage(),
      '/notification': (_) => NotificationPage(),
      '/activity': (_) => ActivityPage(),
      '/schedule': (_) => SchedulePage(),
      '/claim_submit': (_) => ClaimSubmitPage(),
      '/salary': (_) => SalaryPage(),
      '/salary_slip': (_) => SalarySlipPage(),
      '/exception': (_) => ExceptionPage(),
      '/makeexception': (_) => ExceptionAddPage(),
      '/break': (_) => BreakPage(coord: getLocation(context)),
      '/overwork': (_) =>
          OverWorkPage(camera: widget.camera, coord: getLocation(context)),
      '/makeoverwork': (_) => OverWorkAddPage(),
      '/overwork_start': (_) =>
          OverWorkStartPage(camera: widget.camera, coord: getLocation(context)),
      '/overwork_end': (_) =>
          OverWorkEndPage(camera: widget.camera, coord: getLocation(context)),
      '/breakend': (_) => BreakEndPage(camera: widget.camera),
      '/leave': (_) => LeavePage(),
      '/leaveapply': (_) => LeaveApplyPage(),
      '/login': (_) => LoginPage(),
      '/calendar': (_) => CalendarPage(),
      '/permission': (_) => PermissionPage(),
      '/short_permission': (_) => ShortPermissionPage(),
      '/long_permission': (_) => LongPermissionPage(),
      '/permission_success': (_) => PermissionSuccessPage(),
      '/signin': (_) => SignInPage(camera: widget.camera),
      '/signout': (_) => SignOutPage(camera: widget.camera),
      '/permission_handle': (_) => PermissionHandlePage(
        createdAt: "",
        duration: 0,
        start: "",
        end: "",
        jamMasuk: "",
        jamKeluar: "",
        catatan: "",
        requestIzinId: "",
      ),
    };	
  }

	@override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Flutter Demo',
      routes: createRoute(context),
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.deepPurple),
      ),
    );
  }

}