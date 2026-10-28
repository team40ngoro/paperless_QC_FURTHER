<style>
.stepper-wrapper {
  display: flex;
  justify-content: space-between;
  margin-bottom: 30px;
  padding: 0 20px;
}
.stepper-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.stepper-item::before {
  position: absolute;
  content: "";
  border-bottom: 3px solid #e0e0e0;
  width: 100%;
  top: 16px;
  left: -50%;
  z-index: 2;
}

.stepper-item::after {
  position: absolute;
  content: "";
  border-bottom: 3px solid #e0e0e0;
  width: 100%;
  top: 16px;
  left: 50%;
  z-index: 2;
}

.stepper-item .step-counter {
  position: relative;
  z-index: 5;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: #e0e0e0;
  color: white;
  font-weight: bold;
  font-size: 14px;
}

/* State: Completed */
.stepper-item.completed .step-counter {
  background-color: #20c997; /* Teal color */
}

/* Fix connection lines for completed items */
.stepper-item.completed::after {
  border-bottom: 3px solid #20c997;
  z-index: 3;
}
.stepper-item.completed + .stepper-item::before {
  border-bottom: 3px solid #20c997;
  z-index: 3;
}

/* Remove line for first and last */
.stepper-item:first-child::before {
  content: none;
}
.stepper-item:last-child::after {
  content: none;
}

/* State: Active */
.stepper-item.active .step-counter {
  background-color: #20c997; /* Teal color */
}

.step-name {
  margin-top: 10px;
  font-size: 14px;
  font-weight: 500;
  color: #6c757d;
  text-align: center;
}

.stepper-item.active .step-name, 
.stepper-item.completed .step-name {
    color: #333;
    font-weight: 600;
}

.stepper-item a:hover .step-counter {
    background-color: #17a2b8;
    transform: scale(1.1);
    transition: all 0.3s ease;
}

.stepper-item a:hover .step-name {
    color: #17a2b8;
    transition: all 0.3s ease;
}

.stepper-item a .step-counter, .stepper-item a .step-name {
    transition: all 0.3s ease;
}
</style>

<div class="stepper-wrapper">
  <div class="stepper-item {{ $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' }}">
    @if(isset($bahanBakuUuid) && $bahanBakuUuid)
      <a href="{{ route('bahan-baku-tumbling.edit', $bahanBakuUuid) }}" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; width: 100%;">
    @endif
    <div class="step-counter">
        @if($step > 1)
        <i class="fas fa-check"></i>
        @else
        1
        @endif
    </div>
    <div class="step-name">Bahan Baku Tumbling</div>
    @if(isset($bahanBakuUuid) && $bahanBakuUuid)
      </a>
    @endif
  </div>
  <div class="stepper-item {{ $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' }}">
    @if(isset($prosesTumblingUuid) && $prosesTumblingUuid)
      <a href="{{ route('proses-tumbling.edit', $prosesTumblingUuid) }}" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; width: 100%;">
    @elseif(isset($bahanBakuUuid) && $bahanBakuUuid)
      <a href="{{ route('proses-tumbling.create', ['bahan_baku_uuid' => $bahanBakuUuid]) }}" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; width: 100%;">
    @endif
    <div class="step-counter">
        @if($step > 2)
        <i class="fas fa-check"></i>
        @else
        2
        @endif
    </div>
    <div class="step-name">Proses Tumbling</div>
    @if((isset($prosesTumblingUuid) && $prosesTumblingUuid) || (isset($bahanBakuUuid) && $bahanBakuUuid))
      </a>
    @endif
  </div>
  <div class="stepper-item {{ $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' }}">
    @if(isset($prosesAgingUuid) && $prosesAgingUuid)
      <a href="{{ route('proses-aging.edit', $prosesAgingUuid) }}" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; width: 100%;">
    @elseif(isset($prosesTumblingId) && isset($prosesTumblingUuid) && $prosesTumblingId && $prosesTumblingUuid)
      <a href="{{ route('proses-aging.create', ['proses_tumbling_id' => $prosesTumblingId, 'proses_tumbling_uuid' => $prosesTumblingUuid]) }}" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; width: 100%;">
    @endif
    <div class="step-counter">
        @if($step > 3)
        <i class="fas fa-check"></i>
        @else
        3
        @endif
    </div>
    <div class="step-name">Proses Aging</div>
    @if((isset($prosesAgingUuid) && $prosesAgingUuid) || (isset($prosesTumblingId) && isset($prosesTumblingUuid) && $prosesTumblingId && $prosesTumblingUuid))
      </a>
    @endif
  </div>
</div>
