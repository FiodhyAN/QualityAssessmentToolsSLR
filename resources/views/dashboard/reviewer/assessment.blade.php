@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/formpart.css') }}">
@endsection
@section('container')
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50"
                            class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                            style="width: 0%"></div>
                    </div>
                    <div id="qbox-container">
                        <form class="needs-validation assessment_form" id="form-wrapper" method="post" name="form-wrapper" novalidate>
                            <div id="steps-container">
                                @foreach ($questionaires as $question)
                                    <div class="step">
                                        <h3>{{ $question->name }}</h3>
                                        <h4>{{ $question->question }}</h4>
                                        <div class="form-check ps-0 q-box">
                                            <div class="q-box__question">
                                                <input class="form-check-input question__input"
                                                    id="q_{{ $loop->iteration }}_pos" name="{{ $question->name }}"
                                                    type="radio" value="1">
                                                <label class="form-check-label question__label"
                                                    for="q_{{ $loop->iteration }}_pos">{{ $question->pos_answer }}</label>
                                            </div>
                                            <div class="q-box__question">
                                                <input class="form-check-input question__input"
                                                    id="q_{{ $loop->iteration }}_net" name="{{ $question->name }}"
                                                    type="radio" value="0">
                                                <label class="form-check-label question__label"
                                                    for="q_{{ $loop->iteration }}_net">{{ $question->net_answer }}</label>
                                            </div>
                                            <div class="q-box__question">
                                                <input class="form-check-input question__input" id="q_{{ $loop->iteration }}_neg" name="{{ $question->name }}" type="radio" value="-1">
                                                <label class="form-check-label question__label"
                                                    for="q_{{ $loop->iteration }}_neg">{{ $question->neg_answer }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="step">
                                    <div class="mt-1">
                                        <div class="closing-text">
                                            <h4>Assessment Selesai! Terima Kasih!</h4>
                                            <p>Click tombol submit untuk melanjutkan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="q-box__buttons">
                                <button id="prev-btn" type="button">Previous</button>
                                <button id="next-btn" type="button">Next</button>
                                <button id="submit-btn" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h1>Article Assessment</h1>
    <hr>


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="assessment_table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID - No</th>
                            <th>Title</th>
                            <th>Project Name</th>
                            <th>Year</th>
                            <th>Publication</th>
                            <th>Authors</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var table = $('#assessment_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('assessment.table') }}',
            columns: [{
                    data: 'no',
                    name: 'no'
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function(data, type, row) {
                        return '<span style="white-space: normal;">' + data + '</span>"';
                    }
                },
                {
                    data: 'project_name',
                    name: 'project_name'
                },
                {
                    data: 'year',
                    name: 'year'
                },
                {
                    data: 'publication',
                    name: 'publication',
                    render: function(data, type, row) {
                        return '<span style="white-space: normal;">' + data + '</span>"';
                    }
                },
                {
                    data: 'authors',
                    name: 'authors',
                    render: function(data, type, row) {
                        return '<span style="white-space: normal;">' + data + '</span>"';
                    }
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });


        $('#exampleModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('article_id');
            var no = $(event.relatedTarget).data('article_no');

            // add to modal title
            $(this).find('.modal-title').text('Assess Article ' + id + ' - ' + no);
        });

        let step = document.getElementsByClassName('step');
            let prevBtn = document.getElementById('prev-btn');
            let nextBtn = document.getElementById('next-btn');
            let submitBtn = document.getElementById('submit-btn');
            let form = document.getElementsByTagName('form')[0];
            let preloader = document.getElementById('preloader-wrapper');
            let bodyElement = document.querySelector('body');
            let succcessDiv = document.getElementById('success');
    
            form.onsubmit = () => {
                return false
            }
            let current_step = 0;
            const stepCount = {{ count($questionaires) }}
            step[current_step].classList.add('d-block');
            if (current_step == 0) {
                prevBtn.classList.add('d-none');
                submitBtn.classList.add('d-none');
                nextBtn.classList.add('d-inline-block');
            }
    
            const progress = (value) => {
                document.getElementsByClassName('progress-bar')[0].style.width = `${value}%`;
            }
    
            nextBtn.addEventListener('click', () => {
                current_step++;
                let previous_step = current_step - 1;
                if ((current_step > 0) && (current_step <= stepCount)) {
                    prevBtn.classList.remove('d-none');
                    prevBtn.classList.add('d-inline-block');
                    step[current_step].classList.remove('d-none');
                    step[current_step].classList.add('d-block');
                    step[previous_step].classList.remove('d-block');
                    step[previous_step].classList.add('d-none');
                    if (current_step == stepCount) {
                        submitBtn.classList.remove('d-none');
                        submitBtn.classList.add('d-inline-block');
                        nextBtn.classList.remove('d-inline-block');
                        nextBtn.classList.add('d-none');
                    }
                } else {
                    if (current_step > stepCount) {
                        form.onsubmit = () => {
                            return true
                        }
                    }
                }
                progress((100 / stepCount) * current_step);
            });
    
    
            prevBtn.addEventListener('click', () => {
                if (current_step > 0) {
                    current_step--;
                    let previous_step = current_step + 1;
                    prevBtn.classList.add('d-none');
                    prevBtn.classList.add('d-inline-block');
                    step[current_step].classList.remove('d-none');
                    step[current_step].classList.add('d-block');
                    step[previous_step].classList.remove('d-block');
                    step[previous_step].classList.add('d-none');
                    if (current_step < stepCount) {
                        submitBtn.classList.remove('d-inline-block');
                        submitBtn.classList.add('d-none');
                        nextBtn.classList.remove('d-none');
                        nextBtn.classList.add('d-inline-block');
                        prevBtn.classList.remove('d-none');
                        prevBtn.classList.add('d-inline-block');
                    }
                }
    
                if (current_step == 0) {
                    prevBtn.classList.remove('d-inline-block');
                    prevBtn.classList.add('d-none');
                }
                progress((100 / stepCount) * current_step);
            });

            //on close modal progress bar reset
            $('#exampleModal').on('hidden.bs.modal', function () {
                progress(0);
                current_step = 0;
                step[current_step].classList.add('d-block');
                step[current_step].classList.remove('d-none');
                nextBtn.classList.remove('d-none');
                  nextBtn.classList.add('d-inline-block');
                // for each step beside the first one hide it
                  for (let i = 1; i < stepCount+1; i++) {
                     step[i].classList.add('d-none');
                     step[i].classList.remove('d-block');
                  }
                  // remove all checked radio button
                  let radioBtns = document.querySelectorAll('input[type="radio"]');
                  radioBtns.forEach((radioBtn) => {
                      radioBtn.checked = false;
                  });
                if (current_step == 0) {
                    prevBtn.classList.add('d-none');
                    submitBtn.classList.add('d-none');
                    nextBtn.classList.add('d-inline-block');
                }
            });
    </script>
@endsection
