import 'package:hydrated_riverpod/hydrated_riverpod.dart';
import 'package:intl/intl.dart';

// typedef GlobalState = Map<String, Map<String, dynamic>>;
typedef Task = ({List<String> started, List<String> finished});
typedef XPresence = ({String ci, String co});
typedef Break = ({bool onBreak, String startFrom});
typedef Holiday = ({bool holiday, bool workDay});
typedef Auth = ({bool loggedIn, String date});
typedef Status = ({bool signedIn, bool signedOut});
typedef OverWork = ({bool onOverWork});
typedef Exception = ({List<String> list});
typedef Csh = ({bool allowed});


typedef Config = ({
  bool ffocia,
  bool ffocoa,
  int coLimit,
  int ciLimit,
  int tolerance,
});


typedef Company = ({
  String id,
  String name,
  String logo,
  String address,
  int salaryDate,
});

typedef Schedule = ({
  String start,
  String nextStart,
  String finish,
  String breakStart,
  String breakFinish,
  String workSystem,
  String workSystemName,
});

typedef Location = ({List<Map<String, dynamic>> list});
typedef DEVICEPST = ({double lat, double lon});
typedef Coordinate = ({double lat, double lon});
typedef Permission = ({int id});
typedef Reminder = ({double lastLat, double lastLon});

typedef Other = ({
  String pegawaiId,
  String namaPegawai,
  String nomorPegawai,
  String emailPegawai,
  String fotoPegawai,
  String position,
  String status,
});

typedef GlobalState = ({
  Auth auth,
  Status status,
  Company company,
  Schedule schedule,
  Location location,
  DEVICEPST position,
  List<String> history,
  Other other,
  Permission permission,
  Coordinate coordinate,
  Holiday holiday,
  Break breakInfo,
  OverWork overWork,
  Config config,
  Task task,
  Exception exception,
  Csh csh,
  Reminder reminder,
  XPresence presence
});

final globalStateProvider =
    StateNotifierProvider<GlobalStateProvider, GlobalState>((ref) {
      return GlobalStateProvider((
        history: [],
        permission: (id: 0),
        auth: (loggedIn: false, date: ''),
        status: (signedIn: false, signedOut: false),
        company: (id: '', name: '', logo: '', address: '', salaryDate: 0),
        schedule: (
          start: '',
          nextStart: '',
          finish: '',
          breakStart: '',
          breakFinish: '',
          workSystem: '',
          workSystemName: '',
        ),
        location: (list: []),
        position: (lat: 0, lon: 0),
        coordinate: (lat: 0, lon: 0),
        other: (
          pegawaiId: '',
          namaPegawai: '',
          nomorPegawai: '',
          emailPegawai: '',
          fotoPegawai: '',
          position: '',
          status: '',
        ),
        holiday: (holiday: false, workDay: true),
        breakInfo: (onBreak: false, startFrom: ''),
        overWork: (onOverWork: false),
        config: (
          ffocia: false,
          ffocoa: false,
          coLimit: 0,
          ciLimit: 0,
          tolerance: 0,
        ),
        task: (started: [], finished: []),
        exception: (list: []),
        csh: (allowed: false),
        reminder:(lastLat:0,lastLon:0),
        presence:(ci:"00:00",co:"00:00")
      ));
    });

class GlobalStateProvider extends HydratedStateNotifier<GlobalState> {
  GlobalStateProvider(super.state);

  login(GlobalState param) {
    state = param;
  }

  startTask(String id,double lat, double lon) {
    final newStartedTaskList = [...state.task.started, id];
    final finishedTaskList = state.task.finished;

    state = (
      auth: state.auth,
      status: state.status,
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: (started: newStartedTaskList, finished: finishedTaskList),
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder: (lastLat:lat,lastLon:lon),
      presence: state.presence
    );
  }

  taskFinish(String id) {
    final newFinishedTaskList = [...state.task.finished, id];
    final startedTaskList = state.task.started;

    state = (
      auth: state.auth,
      status: state.status,
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: (started: startedTaskList, finished: newFinishedTaskList),
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:(lastLat:0,lastLon:0),
      presence: state.presence
    );
  }

  addHistory(String id) {
    final newHistory = [...state.history, id];

    state = (
      auth: state.auth,
      status: state.status,
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: newHistory,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  logout() {
    state = (
      history: [],
      permission: (id: 0),
      auth: (loggedIn: false, date: ''),
      status: (signedIn: false, signedOut: false),
      company: (id: '', name: '', logo: '', address: '', salaryDate: 0),
      schedule: (
        start: '',
        nextStart: '',
        finish: '',
        breakStart: '',
        breakFinish: '',
        workSystem: '',
        workSystemName: '',
      ),
      location: (list: []),
      position: (lat: 0, lon: 0),
      coordinate: (lat: 0, lon: 0),
      other: (
        pegawaiId: '',
        namaPegawai: '',
        nomorPegawai: '',
        emailPegawai: '',
        fotoPegawai: '',
        position: '',
        status: '',
      ),
      holiday: (holiday: false, workDay: true),
      breakInfo: (onBreak: false, startFrom: ''),
      overWork: (onOverWork: false),
      config: (
        ffocia: false,
        ffocoa: false,
        coLimit: 0,
        ciLimit: 0,
        tolerance: 0,
      ),
      task: (started: [], finished: []),
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence,
    );
  }

  setPosition(double lat, double lon) {
    state = (
      auth: state.auth,
      status: state.status,
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: (lat: lat, lon: lon),
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  setCoordinate(double lat, double lon) {
    state = (
      auth: state.auth,
      status: state.status,
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: (lat: lat, lon: lon),
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  signIn(String formattedTime) {
    state = (
      auth: state.auth,
      status: (signedIn: true, signedOut: false),
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:(ci:formattedTime,co:state.presence.co)
    );
  }

  signOut(formattedTime) {
    state = (
      auth: state.auth,
      status: (signedIn: true, signedOut: true),
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: false),
      reminder:state.reminder,
      presence:(ci:state.presence.ci,co:formattedTime)

    );
  }

  fFOCIMakeAllowed() {
    final Status status = (signedIn: state.status.signedIn, signedOut: false);

    final Config config = (
      ffocia: true,
      ffocoa: state.config.ffocoa,
      coLimit: state.config.coLimit,
      ciLimit: state.config.ciLimit,
      tolerance: state.config.tolerance,
    );

    state = (
      auth: state.auth,
      status: status,
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  fFOCOMakeAllowed() {
    final Status status = (
      signedIn: state.status.signedIn,
      signedOut: state.status.signedOut,
    );

    final Config config = (
      ffocia: state.config.ffocia,
      ffocoa: true,
      coLimit: state.config.coLimit,
      ciLimit: state.config.ciLimit,
      tolerance: state.config.tolerance,
    );

    state = (
      auth: state.auth,
      status: status,
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  breakStart() {
    DateTime now = DateTime.now();
    String from = DateFormat('HH:mm').format(now);

    final Break breakInfo = (onBreak: true, startFrom: from);

    state = (
      auth: state.auth,
      status: (signedIn: true, signedOut: false),
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  overWorkStart() {
    final OverWork overWork = (onOverWork: true);

    state = (
      auth: state.auth,
      status: (signedIn: true, signedOut: true),
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  overWorkEnd() {
    final OverWork overWork = (onOverWork: false);

    state = (
      auth: state.auth,
      status: (signedIn: true, signedOut: true),
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  breakEnd() {
    final from = state.breakInfo.startFrom;
    final Break breakInfo = (onBreak: false, startFrom: from);
    state = (
      auth: state.auth,
      status: (signedIn: true, signedOut: false),
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: state.exception,
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  addException(String identifier) {
    final newExceptionList = [...state.exception.list, identifier];

    state = (
      auth: state.auth,
      status: (signedIn: false, signedOut: false),
      company: state.company,
      schedule: state.schedule,
      location: state.location,
      position: state.position,
      other: state.other,
      history: state.history,
      permission: state.permission,
      coordinate: state.coordinate,
      holiday: state.holiday,
      breakInfo: state.breakInfo,
      overWork: state.overWork,
      config: state.config,
      task: state.task,
      exception: (list: newExceptionList),
      csh: (allowed: state.csh.allowed),
      reminder:state.reminder,
      presence:state.presence
    );
  }

  @override
  GlobalState? fromJson(Map<String, dynamic> json) {
    final authJson = json['auth'] ?? {};
    final statusJson = json['status'] ?? {};
    final companyJson = json['company'] ?? {};
    final scheduleJson = json['schedule'] ?? {};
    final locationJson = json['location'] ?? {};
    final positionJson = json['position'] ?? {};
    final coordinateJson = json['coordinate'] ?? {};
    final otherJson = json['other'] ?? {};
    final permissionJson = json['permission'] ?? {};
    final locationList = locationJson['list'] as List;
    final holidayJson = json['holiday'] ?? {};
    final breakInfoJson = json['breakInfo'] ?? {};
    final overWorkJson = json['overWork'] ?? {};
    final configJson = json['config'] ?? {};
    final taskJson = json['task'] ?? {};
    final exceptionJson = json['exception'] ?? {};
    final cshJson = json['csh'] ?? {};
    final reminderJson = json['reminder'] ?? {};
    final presenceJson = json['presence'] ?? {};

    return (
      auth: (
        loggedIn: authJson['loggedIn'] as bool,
        date: authJson['date'] as String,
      ),
      status: (
        signedIn: statusJson['signedIn'] as bool,
        signedOut: statusJson['signedOut'] as bool,
      ),
      company: (
        id: companyJson['id'] as String,
        name: companyJson['name'] as String,
        logo: companyJson['logo'] as String,
        address: companyJson['address'] as String,
        salaryDate: companyJson['salaryDate'] as int,
      ),
      schedule: (
        start: scheduleJson['start'] as String,
        nextStart: scheduleJson['nextStart'] as String,
        finish: scheduleJson['finish'] as String,
        breakStart: scheduleJson['breakStart'] as String,
        breakFinish: scheduleJson['breakFinish'] as String,
        workSystem: scheduleJson['workSystem'] as String,
        workSystemName: scheduleJson['workSystemName'] as String,
      ),
      location: (
        list: locationList.map((e) => Map<String, dynamic>.from(e)).toList(),
      ),
      position: (
        lat: (positionJson['lat'] as num).toDouble(),
        lon: (positionJson['lon'] as num).toDouble(),
      ),
      coordinate: (
        lat: (coordinateJson['lat'] as num).toDouble(),
        lon: (coordinateJson['lon'] as num).toDouble(),
      ),
      other: (
        pegawaiId: otherJson['pegawaiId'] as String,
        namaPegawai: otherJson['namaPegawai'] as String,
        nomorPegawai: otherJson['nomorPegawai'] as String,
        emailPegawai: otherJson['emailPegawai'] as String,
        fotoPegawai: otherJson['fotoPegawai'] as String,
        position: otherJson['position'] as String,
        status: otherJson['status'] as String,
      ),
      permission: (id: permissionJson['id'] as int),
      history: [
        // action history
      ],
      holiday: (
        holiday: holidayJson['holiday'] as bool,
        workDay: holidayJson['workDay'] as bool,
      ),
      breakInfo: (
        onBreak: breakInfoJson['onBreak'] as bool,
        startFrom: breakInfoJson['startFrom'] as String,
      ),
      overWork: (onOverWork: overWorkJson['onOverWork'] as bool),
      config: (
        ffocia: configJson['ffocia'] as bool,
        ffocoa: configJson['ffocoa'] as bool,
        coLimit: configJson['coLimit'] as int,
        ciLimit: configJson['ciLimit'] as int,
        tolerance: configJson['tolerance'] as int,
      ),
      task: (
        started: List<String>.from(taskJson['started'] ?? []),
        finished: List<String>.from(taskJson['finished'] ?? []),
      ),
      exception: (list: List<String>.from(exceptionJson['list'] ?? [])),
      csh: (allowed: cshJson['allowed'] as bool),
      reminder:(
        lastLat:reminderJson['lastLat'] as double,
        lastLon:reminderJson['lastLon'] as double
      ),
      presence:(
        ci:presenceJson['ci'] as String,
        co:presenceJson['co'] as String,
      )
    );
  }

  @override
  Map<String, dynamic>? toJson(GlobalState state) {
    return {
      'auth': {'loggedIn': state.auth.loggedIn, 'date': state.auth.date},
      'status': {
        'signedIn': state.status.signedIn,
        'signedOut': state.status.signedOut,
      },
      'company': {
        'id': state.company.id,
        'name': state.company.name,
        'logo': state.company.logo,
        'address': state.company.address,
        'salaryDate': state.company.salaryDate,
      },
      'schedule': {
        'start': state.schedule.start,
        'nextStart': state.schedule.nextStart,
        'finish': state.schedule.finish,
        'breakStart': state.schedule.breakStart,
        'breakFinish': state.schedule.breakFinish,
        'workSystem': state.schedule.workSystem,
        'workSystemName': state.schedule.workSystemName,
      },
      'location': {'list': state.location.list},
      'position': {'lat': state.position.lat, 'lon': state.position.lon},
      'coordinate': {'lat': state.coordinate.lat, 'lon': state.coordinate.lon},
      'other': {
        'pegawaiId': state.other.pegawaiId,
        'namaPegawai': state.other.namaPegawai,
        'nomorPegawai': state.other.nomorPegawai,
        'emailPegawai': state.other.emailPegawai,
        'fotoPegawai': state.other.fotoPegawai,
        'position': state.other.position,
        'status': state.other.status,
      },
      'permission': {'id': state.permission.id},
      'holiday': {
        'holiday': state.holiday.holiday,
        'workDay': state.holiday.workDay,
      },
      'breakInfo': {
        'onBreak': state.breakInfo.onBreak,
        'startFrom': state.breakInfo.startFrom,
      },
      'overWork': {'onOverWork': state.overWork.onOverWork},
      'config': {
        'ffocia': state.config.ffocia,
        'ffocoa': state.config.ffocoa,
        'coLimit': state.config.coLimit,
        'ciLimit': state.config.ciLimit,
        'tolerance': state.config.tolerance,
      },
      'task': {'started': state.task.started, 'finished': state.task.finished},
      'exception': {'list': state.exception.list},
      'csh': {'allowed': state.csh.allowed},
      'reminder':{'lastLat':state.reminder.lastLat,'lastLon':state.reminder.lastLon},
      'presence':{'ci':state.presence.ci,'co':state.presence.co}
    };
  }
}
