async function connectBluetooth() {
    try {
      const device = await navigator.bluetooth.requestDevice({
        filters: [{ services: ['heart_rate'] }],
        optionalServices: ['battery_service']
      });
  
      const server = await device.gatt.connect();
      const service = await server.getPrimaryService('heart_rate');
      const characteristic = await service.getCharacteristic('heart_rate_measurement');
  
      await characteristic.startNotifications();
      
      characteristic.addEventListener('characteristicvaluechanged', (event) => {
        const value = event.target.value;
        const lactateLevel = parseLactateData(value);
        updateLiveData(lactateLevel);
      });
  
    } catch (error) {
      console.error('فشل الاتصال:', error);
    }
  }
  
  function parseLactateData(dataView) {
    // هنا تقوم بتحويل البيانات الخام من الجهاز إلى قيمة اللاكتات
    return dataView.getUint8(0) / 10; // مثال لتحويل البيانات
  }
  
  function updateLiveData(value) {
    // تحديث الواجهة بالبيانات الجديدة
    const latestData = document.querySelector('.player-item:first-child .value');
    latestData.textContent = `${value} mmol/L`;
    
    if (value > 4.0) {
      showAlert(`تحذير: مستوى اللاكتات ${value} mmol/L`);
    }
  }